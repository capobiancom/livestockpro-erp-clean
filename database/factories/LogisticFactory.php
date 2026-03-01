<?php

namespace Database\Factories;

use App\Models\Logistic;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogisticFactory extends Factory
{
    protected $model = Logistic::class;

    public function definition(): array
    {
        $animals = Animal::factory()->count($this->faker->numberBetween(1, 5))->create()->pluck('id')->toArray();

        return [
            'reference' => strtoupper($this->faker->unique()->bothify('LOG-####')),
            'vehicle' => $this->faker->bothify('VH-###'),
            'driver' => $this->faker->name(),
            'purpose' => $this->faker->randomElement(['transport', 'market', 'breeding', 'sale']),
            'from_location' => $this->faker->city(),
            'to_location' => $this->faker->city(),
            'departure_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'arrival_at' => $this->faker->dateTimeBetween('now', '+2 days'),
            'animals_count' => count($animals),
            'animal_ids' => $animals,
            'cost' => $this->faker->randomFloat(2, 0, 2000),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
