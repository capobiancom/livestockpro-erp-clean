<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayConfig;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentGatewaySettingsController extends Controller
{
    public function index(Request $request)
    {
        $configs = PaymentGatewayConfig::query()
            ->orderByDesc('id')
            ->get()
            ->keyBy('gateway');

        $gateways = ['bkash', 'stripe', 'cod'];

        $data = collect($gateways)->map(function (string $gateway) use ($configs) {
            $cfg = $configs->get($gateway);

            return [
                'gateway' => $gateway,
                'is_enabled' => (bool) ($cfg?->is_enabled ?? false),
                'is_default' => (bool) ($cfg?->is_default ?? false),
                'config' => $cfg?->config ?? null,
            ];
        })->values();

        return Inertia::render('Settings/PaymentGateways', [
            'gateways' => $data,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'gateways' => ['required', 'array'],
            'gateways.*.gateway' => ['required', 'string', 'in:bkash,stripe,cod'],
            'gateways.*.is_enabled' => ['required', 'boolean'],
            'default_gateway' => ['nullable', 'string', 'in:bkash,stripe,cod'],
            'gateways.*.config' => ['nullable', 'array'],
        ]);

        $defaultGateway = $validated['default_gateway'] ?? null;

        foreach ($validated['gateways'] as $gw) {
            PaymentGatewayConfig::query()->updateOrCreate(
                [
                    'gateway' => $gw['gateway'],
                ],
                [
                    'is_enabled' => (bool) $gw['is_enabled'],
                    'is_default' => $defaultGateway === $gw['gateway'],
                    'config' => $gw['config'] ?? null,
                ]
            );
        }

        // Ensure only one default globally
        if ($defaultGateway) {
            PaymentGatewayConfig::query()
                ->where('gateway', '!=', $defaultGateway)
                ->update(['is_default' => false]);
        }

        return back()->with('success', 'Payment gateway settings updated.');
    }
}
