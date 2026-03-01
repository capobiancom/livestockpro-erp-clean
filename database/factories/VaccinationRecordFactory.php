<?php

namespace Database\Factories;

use App\Models\VaccinationRecord;
use App\Models\Animal;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\VaccineType;

class VaccinationRecordFactory extends Factory
{
    protected $model = VaccinationRecord::class;

    public function definition(): array
    {
        $admin = $this->faker->dateTimeBetween('-1 year', 'now');
        return [
            'animal_id' => Animal::factory(),
            'administered_at' => $admin->format('Y-m-d'),
            'next_due_at' => $this->faker->dateTimeBetween($admin, '+1 year')->format('Y-m-d'),
            'staff_id' => StaffProfile::factory(),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
