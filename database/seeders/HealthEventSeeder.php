<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthEvent;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\EventType;
use App\Models\HealthIssue;

class HealthEventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $animal = Animal::first();
        $eventType = EventType::first();
        $healthIssue = HealthIssue::first();

        HealthEvent::factory()->count(30)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'event_type_id' => $eventType->id,
            'health_issue_id' => HealthIssue::factory(), // $healthIssue->id,
        ]);
    }
}
