<?php

namespace Database\Factories;

use App\Models\MedicineGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicineGroup>
 */
class MedicineGroupFactory extends Factory
{
    protected $model = MedicineGroup::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farm_id' => \App\Models\Farm::factory(),
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->unique()->word . ' Group',
        ];
    }
}
