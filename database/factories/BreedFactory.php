<?php

namespace Database\Factories;

use App\Models\Breed;
use Illuminate\Database\Eloquent\Factories\Factory;

class BreedFactory extends Factory
{
    protected $model = Breed::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true) . ' ' . $this->faker->randomNumber(5),
            'code' => strtoupper($this->faker->lexify('BRD??') . $this->faker->randomNumber(3)),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
            'description' => $this->faker->optional()->sentence(),
            'characteristics' => [
                'temperament' => $this->faker->randomElement(['calm', 'aggressive', 'docile']),
                'suitable_climate' => $this->faker->randomElement(['tropical', 'temperate', 'dry']),
            ],
            'origin' => $this->faker->randomElement(['local', 'exotic', 'cross']),
            'animal_type' => $this->faker->randomElement(['cow', 'bull']),
        ];
    }
}
