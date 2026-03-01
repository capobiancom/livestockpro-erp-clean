<?php

namespace Database\Factories;

use App\Models\DiseaseTreatment;
use App\Models\HealthIssue;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiseaseTreatmentFactory extends Factory
{
    protected $model = DiseaseTreatment::class;

    public function definition(): array
    {
        return [
            'health_issue_id' => HealthIssue::factory(),
            'health_event_id' => \App\Models\HealthEvent::factory(), // Add health_event_id
            'treatment_id' => Treatment::factory(),
            'farm_id' => \App\Models\Farm::factory(), // Add farm_id
            'user_id' => \App\Models\User::factory(), // Add user_id
            'notes' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement(['planned', 'ongoing', 'completed', 'discontinued']), // Ensure status is populated
        ];
    }
}
