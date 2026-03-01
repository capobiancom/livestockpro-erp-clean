<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ChartOfAccount>
 */
class ChartOfAccountFactory extends Factory
{
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
            'code' => $this->faker->unique()->bothify('???###'),
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(\App\Enums\ChartOfAccountType::class),
            'parent_id' => null, // Can be set later for hierarchical data
            'is_system' => $this->faker->boolean(),
            'is_active' => true,
        ];
    }

    public function forFarm(\App\Models\Farm $farm): static
    {
        return $this->state(fn(array $attributes) => [
            'farm_id' => $farm->id,
        ]);
    }

    public function forUser(\App\Models\User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function withParent(\App\Models\ChartOfAccount $parent): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
