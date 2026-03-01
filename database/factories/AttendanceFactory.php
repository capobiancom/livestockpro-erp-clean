<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        return [
            'staff_id' => StaffProfile::factory(),
            'user_id' => \App\Models\User::factory(),
            'farm_id' => \App\Models\Farm::factory(),
            'date' => $date->format('Y-m-d'),
            'check_in' => $this->faker->time(),
            'check_out' => $this->faker->time(),
            'status' => $this->faker->randomElement(['present', 'absent', 'leave', 'sick', 'other']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
