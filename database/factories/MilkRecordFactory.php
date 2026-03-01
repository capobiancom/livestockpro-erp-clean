<?php

namespace Database\Factories;

use App\Models\MilkRecord;
use App\Models\Animal;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilkRecordFactory extends Factory
{
    protected $model = MilkRecord::class;

    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'quantity_liters' => $this->faker->randomFloat(3, 0.5, 50),
            'staff_id' => StaffProfile::factory(),
            'notes' => $this->faker->optional()->sentence(),
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
