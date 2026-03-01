<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'contact_person' => $this->faker->optional()->name(),
            'notes' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement(['milk_buyer', 'animal_buyer', 'wholesaler']),
        ];
    }

    public function forFarm(Farm $farm): static
    {
        return $this->state(fn() => ['farm_id' => $farm->id]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn() => ['user_id' => $user->id]);
    }
}
