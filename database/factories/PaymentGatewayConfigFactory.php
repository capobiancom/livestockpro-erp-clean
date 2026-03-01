<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentGatewayConfig>
 */
class PaymentGatewayConfigFactory extends Factory
{
    protected $model = PaymentGatewayConfig::class;

    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'gateway' => $this->faker->randomElement(['bkash', 'rocket', 'nagad', 'stripe']),
            'is_enabled' => $this->faker->boolean(70),
            'is_default' => false,
            'config' => [
                'mode' => 'sandbox',
                'public_key' => 'pk_test_' . $this->faker->regexify('[A-Za-z0-9]{24}'),
                'secret_key' => 'sk_test_' . $this->faker->regexify('[A-Za-z0-9]{32}'),
                'webhook_secret' => 'whsec_' . $this->faker->regexify('[A-Za-z0-9]{32}'),
            ],
        ];
    }

    public function gateway(string $gateway): static
    {
        return $this->state(fn() => ['gateway' => $gateway]);
    }

    public function enabled(bool $enabled = true): static
    {
        return $this->state(fn() => ['is_enabled' => $enabled]);
    }

    public function default(): static
    {
        return $this->state(fn() => ['is_default' => true, 'is_enabled' => true]);
    }
}
