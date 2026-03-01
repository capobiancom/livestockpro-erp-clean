<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Breed;
use App\Models\User;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $farms = Farm::all();
        $breeds = Breed::all();

        if ($farms->isEmpty()) {
            $farms = Farm::factory()->count(3)->create();
        }

        if ($breeds->isEmpty()) {
            $breeds = Breed::factory()->count(5)->create();
        }

        foreach ($farms as $farm) {
            // Create a herd for farm
            $num = 20;
            $user = User::first();
            Animal::factory()->count($num)->create([
                'farm_id' => $farm->id,
                // pick random existing breed sometimes
                'breed_id' => Breed::inRandomOrder()->first()->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
