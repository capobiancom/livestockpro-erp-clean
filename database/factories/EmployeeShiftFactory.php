<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeShift>
 */
class EmployeeShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => \App\Models\Employee::factory(),
            'shift_id' => \App\Models\Shift::factory(),
            'user_id' => \App\Models\User::factory(),
            'farm_id' => \App\Models\Farm::factory(),
            'effective_from' => $this->faker->date(),
            'effective_to' => $this->faker->optional()->date(),
        ];
    }
}
