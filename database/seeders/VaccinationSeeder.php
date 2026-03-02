<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaccinationRecord;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\StaffProfile;

class VaccinationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $animal = Animal::first();
        $staff = StaffProfile::first();

        $userId = $user ? $user->id : null;
        $farmId = $farm ? $farm->id : null;
        $animalId = $animal ? $animal->id : null;
        $staffId = $staff ? $staff->id : null;

        VaccinationRecord::factory()->count(20)->create([
            'user_id'   => $userId,
            'farm_id'   => $farmId,
            'animal_id' => $animalId,
            'staff_id'  => $staffId,
            'disease_id' => \App\Models\Disease::factory(),
        ])->each(function ($vaccinationRecord) {
            $medicines = \App\Models\Medicine::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($medicines as $medicine) {
                $vaccinationRecord->medications()->create([
                    'medicine_id' => $medicine->id,
                    'quantity' => rand(1, 10),
                    'dose' => rand(1, 5) . 'ml', // Example dose
                ]);
            }
        });
    }
}
