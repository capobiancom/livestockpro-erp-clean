<?php

namespace Database\Factories;

use App\Models\Herd;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

class HerdFactory extends Factory
{
    protected $model = Herd::class;

    public function definition(): array
    {
        return [
            'farm_id' => \App\Models\Farm::factory(), // Make nullable in factory, will be overridden by explicit value
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word() . ' Herd',
            'code' => strtoupper($this->faker->bothify('HRD-###')),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
