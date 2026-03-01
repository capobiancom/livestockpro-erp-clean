<?php

namespace Database\Factories;

use App\Models\VaccineType;
use Illuminate\Database\Eloquent\Factories\Factory;

class VaccineTypeFactory extends Factory
{
    protected $model = VaccineType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . ' Vaccine',
            'manufacturer' => $this->faker->company(),
            'dose' => $this->faker->randomElement(['1ml', '2ml', '5ml']),
            'doses_per_animal' => $this->faker->numberBetween(1, 3),
            'route' => $this->faker->randomElement(['subcutaneous', 'intramuscular', 'oral']),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
