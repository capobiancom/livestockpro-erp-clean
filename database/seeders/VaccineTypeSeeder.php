<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VaccineType;
use App\Models\User;
use App\Models\Farm;

class VaccineTypeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();

        VaccineType::create([
            'name' => 'Clostridial Vaccine',
            'manufacturer' => 'VetCo',
            'dose' => '2ml',
            'route' => 'subcutaneous',
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
        VaccineType::factory()->count(4)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
    }
}
