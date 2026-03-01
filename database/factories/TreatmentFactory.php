<?php

namespace Database\Factories;

use App\Models\Treatment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreatmentFactory extends Factory
{
    protected $model = Treatment::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' treatment',
            'instructions' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
