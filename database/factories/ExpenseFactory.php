<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Farm;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'amount' => $this->faker->randomFloat(2, 5, 5000),
            'incurred_on' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'farm_id' => \App\Models\Farm::factory(), // Ensure farm_id is created
            'user_id' => \App\Models\User::factory(), // Ensure user_id is created
            'staff_id' => \App\Models\StaffProfile::inRandomOrder()->first()->id, // Select an existing staff_id
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
