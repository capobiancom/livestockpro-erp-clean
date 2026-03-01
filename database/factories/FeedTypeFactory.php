<?php

namespace Database\Factories;

use App\Models\FeedType;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedTypeFactory extends Factory
{
    protected $model = FeedType::class;

    public function definition(): array
    {
        $units = ['kg', 'g', 'liter', 'bag'];
        return [
            'name' => $this->faker->unique()->word() . ' Feed',
            'unit' => $this->faker->randomElement($units),
            'description' => $this->faker->optional()->sentence(),
            'nutrient_info' => [
                'protein' => $this->faker->randomFloat(1, 5, 30),
                'fat' => $this->faker->randomFloat(1, 1, 10),
                'fiber' => $this->faker->randomFloat(1, 1, 20),
            ],
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
