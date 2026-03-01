<?php

namespace Database\Factories;

use App\Models\ReproductionRecord;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReproductionRecordFactory extends Factory
{
    protected $model = ReproductionRecord::class;

    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'event' => $this->faker->randomElement(['mating', 'insemination', 'pregnancy_check', 'calving', 'abortion', 'other']),
            'partner_id' => Animal::factory(),
            'event_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'outcome' => $this->faker->optional()->word(),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
