<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentGatewayConfig;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashGateway
{
    public function __construct(
        private readonly PaymentGatewayConfig $config
    ) {}

    /**
     * bKash Checkout v2 integration.
     *
     * Expected config keys (stored in PaymentGatewayConfig->config):
     * - mode: 'sandbox'|'live' (optional; controller may decide)
     * - sandbox_base_url: e.g. https://tokenized.sandbox.bka.sh/v1.2.0-beta
     * - live_base_url: e.g. https://tokenized.pay.bka.sh/v1.2.0-beta
     * - app_key
     * - app_secret
     * - username
     * - password
     *
     * Notes:
     * - bKash requires an access token (grant token) before create/execute.
     * - This class keeps things stateless; token is fetched per initiation.
     */
    public function createPayment(SubscriptionInvoice $invoice, SubscriptionPayment $payment, string $mode): array
    {
        $cfg = $this->config->config ?? [];

        $baseUrl = $this->baseUrl($mode, $cfg);
        if (! $baseUrl) {
            return [
                'redirect_url' => route('billing.index'),
                'provider' => 'bkash',
                'mode' => $mode,
                'message' => 'bKash base URL missing in gateway config.',
            ];
        }

        $token = $this->grantToken($mode, $cfg);
        if (! $token) {
            return [
                'redirect_url' => route('billing.index'),
                'provider' => 'bkash',
                'mode' => $mode,
                'message' => 'Failed to obtain bKash access token.',
            ];
        }

        $callbackUrl = route('payments.return') . '?gateway=bkash&payment_id=' . $payment->id;

        // bKash expects amount in BDT with 2 decimals (string).
        $amountBdt = number_format(((int) $invoice->total_cents) / 100, 2, '.', '');

        $payload = [
            'mode' => '0011',
            'payerReference' => (string) $invoice->farm_id,
            'callbackURL' => $callbackUrl,
            'amount' => $amountBdt,
            'currency' => strtoupper($invoice->currency ?? 'BDT'),
            'intent' => 'sale',
            'merchantInvoiceNumber' => (string) $invoice->invoice_number,
        ];

        $resp = $this->client($baseUrl, $cfg, $token)
            ->post('/tokenized/checkout/create', $payload);

        if (! $resp->successful()) {
            Log::warning('bKash create payment failed.', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);

            return [
                'redirect_url' => route('billing.index'),
                'provider' => 'bkash',
                'mode' => $mode,
                'message' => 'bKash create payment failed.',
            ];
        }

        $data = $resp->json() ?? [];

        return [
            'provider' => 'bkash',
            'mode' => $mode,
            'bkash_payment_id' => $data['paymentID'] ?? null,
            'redirect_url' => $data['bkashURL'] ?? route('billing.index'),
            'raw' => $data,
        ];
    }

    public function executePayment(string $bkashPaymentId, string $mode): array
    {
        $cfg = $this->config->config ?? [];
        $baseUrl = $this->baseUrl($mode, $cfg);

        $token = $this->grantToken($mode, $cfg);
        if (! $token) {
            return [
                'ok' => false,
                'message' => 'Failed to obtain bKash access token.',
            ];
        }

        $resp = $this->client($baseUrl, $cfg, $token)
            ->post('/tokenized/checkout/execute', [
                'paymentID' => $bkashPaymentId,
            ]);

        return [
            'ok' => $resp->successful(),
            'status' => $resp->status(),
            'data' => $resp->json(),
            'raw' => $resp->body(),
        ];
    }

    private function grantToken(string $mode, array $cfg): ?string
    {
        $baseUrl = $this->baseUrl($mode, $cfg);
        $appKey = $cfg['app_key'] ?? null;
        $appSecret = $cfg['app_secret'] ?? null;
        $username = $cfg['username'] ?? null;
        $password = $cfg['password'] ?? null;

        if (! $baseUrl || ! $appKey || ! $appSecret || ! $username || ! $password) {
            return null;
        }

        $resp = Http::baseUrl($baseUrl)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'X-APP-Key' => $appKey,
            ])
            ->post('/tokenized/checkout/token/grant', [
                'app_key' => $appKey,
                'app_secret' => $appSecret,
                'username' => $username,
                'password' => $password,
            ]);

        if (! $resp->successful()) {
            Log::warning('bKash token grant failed.', [
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);

            return null;
        }

        $data = $resp->json() ?? [];

        return $data['id_token'] ?? $data['token'] ?? null;
    }

    private function client(string $baseUrl, array $cfg, string $token): PendingRequest
    {
        $appKey = $cfg['app_key'] ?? '';

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $appKey,
            ]);
    }

    private function baseUrl(string $mode, array $cfg): ?string
    {
        if ($mode === 'live') {
            return $cfg['live_base_url'] ?? null;
        }

        return $cfg['sandbox_base_url'] ?? null;
    }
}
