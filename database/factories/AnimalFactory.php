<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Breed;
use App\Models\Farm;
use App\Models\Herd;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        $sex = $this->faker->randomElement(['male', 'female']);
        // Reset unique faker generator for each definition call to ensure fresh unique values
        $this->faker->unique(true);
        return [
            // Ensure tag is truly unique by appending a unique ID
            'tag' => strtoupper($this->faker->bothify('TAG-####') . '-' . uniqid()),
            'name' => $this->faker->firstName(),
            'sex' => $sex,
            'dob' => $this->faker->dateTimeBetween('-8 years', '-1 year')->format('Y-m-d'),
            'breed_id' => Breed::factory(),
            'farm_id' => Farm::factory(),
            'user_id' => \App\Models\User::factory(), // Add user_id field
            'herd_id' => null,
            'status' => $this->faker->randomElement(['active', 'sold', 'deceased', 'quarantine', 'other']),
            'current_weight_kg' => $this->faker->randomFloat(2, 50, 800),
            'color' => $this->faker->colorName(),
            'acquired_at' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'supplier_id' => Supplier::factory(),
            'attributes' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
