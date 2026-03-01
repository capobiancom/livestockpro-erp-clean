<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\User;
use App\Models\Farm;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $farms = Farm::all();

        if ($users->isEmpty() || $farms->isEmpty()) {
            echo "Skipping ShiftSeeder: No users or farms found.\n";
            return;
        }

        foreach ($users as $user) {
            foreach ($farms as $farm) {
                Shift::factory()->count(3)->create([
                    'user_id' => $user->id,
                    'farm_id' => $farm->id,
                ]);
            }
        }
    }
}
