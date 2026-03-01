<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewayConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $gateways = [
                'bkash' => [
                    'mode' => 'sandbox',
                    // bKash uses service-based endpoints like:
                    //   https://<service-name>.sandbox.bka.sh
                    //   https://<service-name>.pay.bka.sh
                    // Keep these as placeholders; set real values in admin settings.
                    'base_url' => 'https://checkout.sandbox.bka.sh',
                    'app_key' => 'bkash_dummy_app_key',
                    'app_secret' => 'bkash_dummy_app_secret',
                    'username' => 'bkash_dummy_username',
                    'password' => 'bkash_dummy_password',
                ],
                'stripe' => [
                    'mode' => 'test',
                    'public_key' => 'pk_test_dummy_public_key',
                    'secret_key' => 'sk_test_dummy_secret_key',
                    'webhook_secret' => 'whsec_dummy_webhook_secret',
                ],
                'cod' => null,
            ];

            $defaultGateway = 'stripe';

            foreach ($gateways as $gateway => $config) {
                PaymentGatewayConfig::updateOrCreate(
                    [
                        'gateway' => $gateway,
                    ],
                    [
                        'is_enabled' => $gateway === $defaultGateway,
                        'is_default' => $gateway === $defaultGateway,
                        'config' => $config,
                    ]
                );
            }
        });
    }
}
