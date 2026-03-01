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

        VaccinationRecord::factory()->count(20)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'staff_id' => $staff->id,
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
