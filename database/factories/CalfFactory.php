<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calf>
 */
class CalfFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $birthDate = $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');

        return [
            'farm_id' => \App\Models\Farm::factory(),
            'mother_id' => \App\Models\Animal::factory(),
            'father_id' => \App\Models\Animal::factory(),
            'tag_number' => 'CALF-' . $this->faker->unique()->randomNumber(5), // Generate a unique tag number
            'gender' => $gender,
            'birth_date' => $birthDate,
            'birth_weight' => $this->faker->randomFloat(2, 20, 60),
            'health_status' => $this->faker->randomElement(['healthy', 'weak', 'critical']),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
