<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Designation>
 */
class DesignationFactory extends Factory
{
    protected $model = Designation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle(),
            'level' => $this->faker->numberBetween(1, 10),
            'user_id' => \App\Models\User::factory(),
            'farm_id' => Farm::factory()
        ];
    }
}
