<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayConfig;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use App\Services\Payments\Gateways\BkashGateway;
use App\Services\Payments\Gateways\StripeGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentGatewayController extends Controller
{
    /**
     * Initiate a payment for a subscription invoice.
     *
     * Behavior:
     * - Uses the selected gateway if enabled, otherwise falls back to default enabled gateway.
     * - If live keys are missing (or config mode is sandbox/test), it will run in sandbox mode.
     * - Sandbox mode simulates a successful payment and marks invoice as paid.
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:subscription_invoices,id'],
            'gateway' => ['nullable', 'string', Rule::in(['bkash', 'stripe', 'cod'])],
        ]);

        $user = $request->user();
        $farmId = $user?->farm_id;
        abort_unless($farmId, 422, 'Farm context missing.');

        $invoice = SubscriptionInvoice::query()
            ->where('id', $validated['invoice_id'])
            ->where('farm_id', $farmId)
            ->firstOrFail();

        abort_if($invoice->status === 'paid', 422, 'Invoice already paid.');

        $requestedGateway = $validated['gateway'] ?? null;

        $gateway = $this->resolveGateway($requestedGateway);

        $config = PaymentGatewayConfig::query()
            ->where('gateway', $gateway)
            ->first();

        abort_unless($config?->is_enabled, 422, 'Selected payment gateway is disabled.');

        $mode = $this->resolveMode($gateway, $config?->config ?? []);

        return DB::transaction(function () use ($invoice, $gateway, $mode, $config) {
            // Idempotency: reuse an existing initiated/pending payment for this invoice+gateway
            // to avoid duplicate rows and ensure status transitions are reflected in reporting.
            $payment = SubscriptionPayment::query()
                ->where('farm_id', $invoice->farm_id)
                ->where('subscription_invoice_id', $invoice->id)
                ->where('gateway', $gateway)
                ->whereIn('status', ['initiated', 'pending'])
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                $payment = SubscriptionPayment::query()->create([
                    'farm_id' => $invoice->farm_id,
                    'subscription_invoice_id' => $invoice->id,
                    'gateway' => $gateway,
                    'amount_cents' => $invoice->total_cents,
                    'currency' => $invoice->currency,
                    'status' => 'initiated',
                ]);
            } else {
                // Keep amount/currency in sync with invoice in case invoice was regenerated/updated.
                $payment->update([
                    'amount_cents' => $invoice->total_cents,
                    'currency' => $invoice->currency,
                ]);
            }

            // For now: sandbox/test mode simulates success immediately.
            if (in_array($mode, ['sandbox', 'test'], true)) {
                $payment->update([
                    'status' => 'succeeded',
                ]);

                $invoice->update([
                    'status' => 'paid',
                ]);

                return response()->json([
                    'ok' => true,
                    'mode' => $mode,
                    'gateway' => $gateway,
                    'payment_id' => $payment->id,
                    // After successful subscription payment, send farm owner to admin panel.
                    'redirect_url' => route('dashboard'),
                    'message' => 'Sandbox payment completed.',
                ]);
            }

            if ($gateway === 'stripe') {
                $stripe = new StripeGateway($config);

                $session = $stripe->createCheckoutSession($invoice, $payment);

                // Persist provider reference for later webhook reconciliation
                if (! empty($session['session_id'])) {
                    $payment->update([
                        'provider_payment_id' => $session['session_id'],
                    ]);
                }

                return response()->json([
                    'ok' => true,
                    'mode' => $mode,
                    'gateway' => $gateway,
                    'payment_id' => $payment->id,
                    'redirect_url' => $session['redirect_url'] ?? route('billing.index'),
                    'message' => $session['message'] ?? 'Redirecting to Stripe checkout.',
                ]);
            }

            if ($gateway === 'bkash') {
                $bkash = new BkashGateway($config);

                $created = $bkash->createPayment($invoice, $payment, $mode);

                if (! empty($created['bkash_payment_id'])) {
                    $payment->update([
                        'provider_payment_id' => $created['bkash_payment_id'],
                        'provider_payload' => $created['raw'] ?? null,
                        'status' => 'pending',
                    ]);
                }

                return response()->json([
                    'ok' => true,
                    'mode' => $mode,
                    'gateway' => $gateway,
                    'payment_id' => $payment->id,
                    'redirect_url' => $created['redirect_url'] ?? route('billing.index'),
                    'message' => $created['message'] ?? 'Redirecting to bKash checkout.',
                ]);
            }

            if ($gateway === 'cod') {
                $payment->update([
                    'status' => 'pending',
                    'provider_payload' => [
                        'note' => 'Cash on delivery selected. Awaiting manual confirmation.',
                    ],
                ]);

                return response()->json([
                    'ok' => true,
                    'mode' => $mode,
                    'gateway' => $gateway,
                    'payment_id' => $payment->id,
                    'redirect_url' => route('billing.index'),
                    'message' => 'Cash on delivery selected. Please contact support to confirm payment.',
                ]);
            }

            // Live mode placeholder for other gateways.
            Log::info('Live payment initiation requested (not yet integrated).', [
                'gateway' => $gateway,
                'payment_id' => $payment->id,
            ]);

            return response()->json([
                'ok' => true,
                'mode' => $mode,
                'gateway' => $gateway,
                'payment_id' => $payment->id,
                'redirect_url' => route('billing.index'),
                'message' => 'Live payment initiation created. Gateway integration pending.',
            ]);
        });
    }

    /**
     * Handle user return/callback from gateway (GET).
     * For now, this is a generic endpoint that can be used by gateways that redirect back.
     */
    public function handleReturn(Request $request)
    {
        $gateway = $request->query('gateway');

        if ($gateway === 'bkash') {
            $paymentId = $request->query('payment_id');
            $status = $request->query('status'); // success|failure|cancel (bKash sends these)
            $bkashPaymentId = $request->query('paymentID') ?? $request->query('payment_id');

            if ($paymentId) {
                DB::transaction(function () use ($paymentId, $status, $bkashPaymentId) {
                    $payment = SubscriptionPayment::query()->lockForUpdate()->find($paymentId);
                    if (! $payment) {
                        return;
                    }

                    // If already succeeded, no-op
                    if ($payment->status === 'succeeded') {
                        return;
                    }

                    // If bKash returned cancel/failure, mark failed
                    if (in_array($status, ['cancel', 'failure'], true)) {
                        $payment->update([
                            'status' => 'failed',
                            'provider_payload' => array_merge((array) ($payment->provider_payload ?? []), [
                                'return_status' => $status,
                            ]),
                        ]);

                        return;
                    }

                    // On success, execute payment to confirm
                    $config = PaymentGatewayConfig::query()->where('gateway', 'bkash')->first();
                    if (! $config?->is_enabled) {
                        return;
                    }

                    $mode = $this->resolveMode('bkash', $config->config ?? []);
                    $bkash = new BkashGateway($config);

                    $providerId = $payment->provider_payment_id ?: ($bkashPaymentId ?: null);
                    if (! $providerId) {
                        return;
                    }

                    $executed = $bkash->executePayment($providerId, $mode);

                    $payment->update([
                        'provider_payment_id' => $providerId,
                        'provider_payload' => array_merge((array) ($payment->provider_payload ?? []), [
                            'execute' => $executed,
                        ]),
                    ]);

                    if (($executed['ok'] ?? false) === true) {
                        $payment->update(['status' => 'succeeded']);

                        $invoice = SubscriptionInvoice::query()
                            ->lockForUpdate()
                            ->find($payment->subscription_invoice_id);

                        if ($invoice && $invoice->status !== 'paid') {
                            $invoice->update(['status' => 'paid']);
                        }
                    } else {
                        $payment->update(['status' => 'failed']);
                    }
                });
            }

            return redirect()
                ->route('billing.index')
                ->with('success', 'bKash payment return received.');
        }

        return redirect()
            ->route('billing.index')
            ->with('success', 'Payment return received.');
    }

    /**
     * Webhook endpoint for gateways (POST).
     *
     * Stripe:
     * - Verifies signature using configured webhook_secret
     * - Marks payment succeeded + invoice paid on checkout.session.completed
     * - Idempotent: if already succeeded/paid, no-op
     *
     * Others:
     * - Logs payload and returns 200 (integration pending)
     */
    public function webhook(Request $request, string $gateway)
    {
        abort_unless(in_array($gateway, ['bkash', 'stripe', 'cod'], true), 404);

        if ($gateway === 'bkash') {
            // bKash webhook integration can be added here if you enable it in your merchant panel.
            // For now, we accept and log payloads (same as default behavior below).
            Log::info('bKash webhook received.', [
                'payload' => $request->all(),
            ]);

            return response()->json(['ok' => true]);
        }

        if ($gateway === 'stripe') {
            $config = PaymentGatewayConfig::query()->where('gateway', 'stripe')->first();
            abort_unless($config?->is_enabled, 422, 'Stripe gateway is disabled.');

            $cfg = $config->config ?? [];
            $webhookSecret = $cfg['webhook_secret'] ?? null;
            abort_unless($webhookSecret, 422, 'Stripe webhook secret missing.');

            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');

            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (\Throwable $e) {
                Log::warning('Stripe webhook signature verification failed.', [
                    'error' => $e->getMessage(),
                ]);

                return response()->json(['ok' => false], 400);
            }

            // Persist raw payload for audit/debug
            $type = $event->type ?? null;

            if ($type === 'checkout.session.completed') {
                $session = $event->data->object ?? null;

                $paymentId = $session->metadata->payment_id ?? null;
                $invoiceId = $session->metadata->invoice_id ?? null;

                if ($paymentId && $invoiceId) {
                    DB::transaction(function () use ($paymentId, $invoiceId, $event, $session) {
                        $payment = SubscriptionPayment::query()->lockForUpdate()->find($paymentId);
                        $invoice = SubscriptionInvoice::query()->lockForUpdate()->find($invoiceId);

                        if (! $payment || ! $invoice) {
                            return;
                        }

                        // Store provider ids/payload
                        $payment->update([
                            'provider_payment_id' => $session->id ?? $payment->provider_payment_id,
                            'provider_payload' => $event,
                        ]);

                        if ($payment->status !== 'succeeded') {
                            $payment->update(['status' => 'succeeded']);
                        }

                        if ($invoice->status !== 'paid') {
                            $invoice->update(['status' => 'paid']);
                        }
                    });
                }
            }

            return response()->json(['ok' => true]);
        }

        Log::info('Payment webhook received.', [
            'gateway' => $gateway,
            'payload' => $request->all(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function resolveGateway(?string $requestedGateway): string
    {
        if ($requestedGateway) {
            $isEnabled = PaymentGatewayConfig::query()
                ->where('gateway', $requestedGateway)
                ->where('is_enabled', true)
                ->exists();

            if ($isEnabled) {
                return $requestedGateway;
            }
        }

        $default = PaymentGatewayConfig::query()
            ->where('is_default', true)
            ->where('is_enabled', true)
            ->value('gateway');

        if ($default) {
            return $default;
        }

        $firstEnabled = PaymentGatewayConfig::query()
            ->where('is_enabled', true)
            ->orderBy('gateway')
            ->value('gateway');

        abort_unless($firstEnabled, 422, 'No payment gateway is enabled.');

        return $firstEnabled;
    }

    /**
     * Decide whether to use live or sandbox/test.
     *
     * Rule requested:
     * - If real secret/api key is not provided => sandbox works.
     *
     * Implementation:
     * - If config['mode'] is explicitly sandbox/test => use it.
     * - Else if required live keys are missing/placeholder => sandbox/test.
     * - Else => live.
     */
    private function resolveMode(string $gateway, array $config): string
    {
        $explicitMode = $config['mode'] ?? null;
        if (in_array($explicitMode, ['sandbox', 'test', 'live'], true)) {
            if ($explicitMode !== 'live') {
                return $explicitMode;
            }
            // explicit live: still verify keys exist; otherwise fallback to sandbox/test
        }

        $hasLiveKeys = $this->hasLiveKeys($gateway, $config);

        if (! $hasLiveKeys) {
            // Stripe uses "test" terminology; others use "sandbox"
            return $gateway === 'stripe' ? 'test' : 'sandbox';
        }

        return 'live';
    }

    private function hasLiveKeys(string $gateway, array $config): bool
    {
        // Treat dummy placeholders as missing.
        $isMissing = function (?string $value): bool {
            if (! $value) {
                return true;
            }

            $v = strtolower(trim($value));

            return $v === '' ||
                str_contains($v, 'dummy') ||
                str_contains($v, 'test_') ||
                str_contains($v, 'sandbox');
        };

        return match ($gateway) {
            'stripe' => ! $isMissing($config['secret_key'] ?? null),
            'bkash' => ! $isMissing($config['app_key'] ?? null) && ! $isMissing($config['app_secret'] ?? null),
            'cod' => true,
            default => false,
        };
    }
}
