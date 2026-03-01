<?php

namespace Database\Factories;

use App\Models\HealthEvent;
use App\Models\Animal;
use App\Models\EventType;
use App\Models\StaffProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthEventFactory extends Factory
{
    protected $model = HealthEvent::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-2 years', 'now');
        $resolved = $this->faker->optional()->dateTimeBetween($start, 'now');

        $vetFee = $this->faker->optional(0.7)->randomFloat(2, 0, 1500);
        $labCost = $this->faker->optional(0.5)->randomFloat(2, 0, 800);
        $otherCost = $this->faker->optional(0.6)->randomFloat(2, 0, 700);

        return [
            'animal_id' => Animal::factory(),
            'event_type_id' => EventType::inRandomOrder()->first()->id ?? EventType::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'occurred_at' => $start->format('Y-m-d'),
            'resolved_at' => $resolved ? $resolved->format('Y-m-d') : null,
            'vet_fee' => $vetFee,
            'lab_cost' => $labCost,
            'other_cost' => $otherCost,
            'cost' => (float) ($vetFee ?? 0) + (float) ($labCost ?? 0) + (float) ($otherCost ?? 0),
            'treated_by' => StaffProfile::factory(),
            'farm_id' => \App\Models\Farm::factory(), // Add farm_id
            'health_issue_id' => \App\Models\HealthIssue::factory(), // Add health_issue_id
            'user_id' => \App\Models\User::factory(), // Add user_id
        ];
    }
}
