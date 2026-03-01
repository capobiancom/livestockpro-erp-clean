<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['male', 'female', 'other']);
        $firstName = $this->faker->firstName($gender);
        $lastName = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail();

        return [
            'employee_code' => 'EMP-' . Str::upper(Str::random(5)),
            'farm_id' => Farm::factory(),
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'designation_id' => Designation::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'gender' => $gender,
            'date_of_birth' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $email,
            'address' => $this->faker->address(),
            'join_date' => $this->faker->date(),
            'employment_type' => $this->faker->randomElement(['permanent', 'contract', 'daily']),
            'salary_type' => $this->faker->randomElement(['monthly', 'daily', 'hourly']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'terminated']),
            'bonus' => $this->faker->randomFloat(2, 0, 1000),
            'festival_bonus' => $this->faker->randomFloat(2, 0, 500),
            'performance_incentive' => $this->faker->randomFloat(2, 0, 750),
            'tax_amount' => $this->faker->randomFloat(2, 0, 300),
            'loan_deduction' => $this->faker->randomFloat(2, 0, 200),
            'other_deductions' => $this->faker->randomFloat(2, 0, 100),
            'user_email' => $email,
            'password' => Hash::make('password'), // default password
        ];
    }
}
