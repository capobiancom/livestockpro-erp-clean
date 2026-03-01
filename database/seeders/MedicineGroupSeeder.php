<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\MedicineGroup;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicineGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farm = Farm::first();

        $user = User::first();
        MedicineGroup::factory()->count(5)->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
        ]);
    }
}
