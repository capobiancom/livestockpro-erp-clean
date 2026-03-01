<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StaffProfile;
use App\Models\Farm;
use App\Models\User;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $farms = Farm::all();

        if ($farms->isEmpty()) {
            $farms = Farm::factory()->count(3)->create();
        }

        $user = User::first();
        foreach ($farms as $farm) {
            StaffProfile::factory()->count(5)->create([
                'farm_id' => $farm->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
