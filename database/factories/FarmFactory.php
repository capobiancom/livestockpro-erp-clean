<?php

namespace Database\Factories;

use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmFactory extends Factory
{
    protected $model = Farm::class;

    public function definition(): array
    {
        // Reset unique faker generator for each definition call to ensure fresh unique values
        $this->faker->unique(true);
        return [
            'name' => $this->faker->company . ' Farm',
            // Ensure code is truly unique by appending a unique ID
            'code' => strtoupper($this->faker->lexify('FRM???') . '-' . uniqid()),
            'address' => $this->faker->address(),
            'contact_name' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'user_id' => \App\Models\User::factory(), // Add user_id field
            'metadata' => [
                'notes' => $this->faker->optional()->sentence(),
            ],
        ];
    }

    public function forUser(\App\Models\User $user): static
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
