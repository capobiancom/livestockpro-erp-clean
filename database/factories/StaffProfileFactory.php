<?php

namespace Database\Factories;

use App\Models\StaffProfile;
use App\Models\User;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffProfileFactory extends Factory
{
    protected $model = StaffProfile::class;

    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'user_id' => $user->id,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $user->email,
            'position' => $this->faker->randomElement(['herdsman', 'vet', 'manager', 'driver', 'labour']),
            'farm_id' => null, // Make nullable in factory, will be overridden by explicit value
            'hired_at' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'metadata' => null,
        ];
    }
}
