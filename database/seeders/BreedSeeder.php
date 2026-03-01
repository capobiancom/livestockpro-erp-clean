<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Breed;
use App\Models\User;
use App\Models\Farm;

class BreedSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();

        $breeds = ['Sahiwal', 'Gir', 'Jersey', 'Holstein', 'Ongole', 'Tharparkar', 'Red Sindhi', 'Brown Swiss'];
        foreach ($breeds as $name) {
            Breed::firstOrCreate(
                ['name' => $name, 'farm_id' => $farm->id],
                [
                    'user_id' => $user->id,
                    'origin' => 'local', // Default value for seeder
                    'animal_type' => 'cow', // Default value for seeder
                ]
            );
        }
    }
}
