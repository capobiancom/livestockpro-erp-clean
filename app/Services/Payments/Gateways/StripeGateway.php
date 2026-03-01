<?php

namespace App\Services\Payments\Gateways;

use App\Models\PaymentGatewayConfig;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Stripe;

class StripeGateway
{
    public function __construct(
        private readonly PaymentGatewayConfig $config
    ) {}

    /**
     * Create a Stripe Checkout Session and return a redirect URL.
     *
     * Expects config keys:
     * - public_key (optional for server-side)
     * - secret_key (required for live/test API calls)
     * - webhook_secret (for webhook verification)
     */
    public function createCheckoutSession(SubscriptionInvoice $invoice, SubscriptionPayment $payment): array
    {
        $cfg = $this->config->config ?? [];
        $secretKey = $cfg['secret_key'] ?? null;

        if (! $secretKey) {
            return [
                'redirect_url' => route('billing.index'),
                'provider' => 'stripe',
                'mode' => $cfg['mode'] ?? 'test',
                'message' => 'Stripe secret key missing; cannot create checkout session.',
            ];
        }

        Stripe::setApiKey($secretKey);

        $successUrl = route('payments.return') . '?status=success&payment_id=' . $payment->id;
        $cancelUrl = route('payments.return') . '?status=cancel&payment_id=' . $payment->id;

        $session = StripeCheckoutSession::create([
            'mode' => 'payment',
            'client_reference_id' => (string) $payment->id,
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'invoice_id' => (string) $invoice->id,
                'farm_id' => (string) $invoice->farm_id,
            ],
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => strtolower($invoice->currency ?? 'bdt'),
                        'unit_amount' => (int) $invoice->total_cents,
                        'product_data' => [
                            'name' => 'Farm subscription invoice ' . $invoice->invoice_number,
                        ],
                    ],
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        return [
            'redirect_url' => $session->url,
            'provider' => 'stripe',
            'mode' => $cfg['mode'] ?? 'test',
            'session_id' => $session->id,
        ];
    }
}
