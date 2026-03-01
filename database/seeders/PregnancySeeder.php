<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pregnancy;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\ReproductionRecord;

class PregnancySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $animal = Animal::first();
        $reproductionRecord = ReproductionRecord::first();

        Pregnancy::factory()->count(20)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'reproduction_record_id' => $reproductionRecord->id,
        ]);
    }
}
