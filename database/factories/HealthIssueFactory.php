<?php

namespace Database\Factories;

use App\Models\HealthIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthIssueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HealthIssue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
            'animal_id' => \App\Models\Animal::factory(),
            'disease_id' => \App\Models\Disease::factory(), // Add disease_id
        ];
    }
}
