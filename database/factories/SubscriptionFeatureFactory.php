<?php

namespace Database\Factories;

use App\Models\SubscriptionFeature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SubscriptionFeature>
 */
class SubscriptionFeatureFactory extends Factory
{
    protected $model = SubscriptionFeature::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'key' => Str::slug($name, '_') . '_' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 50),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['is_active' => false]);
    }
}
