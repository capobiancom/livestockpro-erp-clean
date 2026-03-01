<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeDocument>
 */
class EmployeeDocumentFactory extends Factory
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
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
            'document_type' => $this->faker->randomElement(['NID', 'License', 'Certificate']),
            'document_number' => $this->faker->unique()->bothify('##########'),
            'expiry_date' => $this->faker->date(),
            'file_path' => $this->faker->filePath(),
        ];
    }
}
