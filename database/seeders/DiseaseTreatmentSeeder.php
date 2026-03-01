<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiseaseTreatment;
use App\Models\HealthIssue;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Farm;

class DiseaseTreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are health issues and treatments to associate
        if (HealthIssue::count() === 0) {
            HealthIssue::factory()->count(10)->create();
        }
        if (Treatment::count() === 0) {
            Treatment::factory()->count(10)->create();
        }

        $user = User::first();
        $farm = Farm::first();
        DiseaseTreatment::factory()->count(20)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
    }
}
