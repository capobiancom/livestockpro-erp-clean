<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\Breed;
use App\Models\User;
use App\Models\Supplier;

class AnimalSeeder extends Seeder
{
    public function run(): void
    {
        $farms = Farm::all();
        $breeds = Breed::all();
        $user = User::first();

        if (!$user) {
            $user = User::factory()->create([
                'email' => 'superuser@livestockproerp.com',
                'name' => 'Super Admin',
            ]);
        }

        if ($farms->isEmpty()) {
            $farms = Farm::factory()->count(3)->create(['user_id' => $user->id]);
        }

        if ($breeds->isEmpty()) {
            $breeds = Breed::factory()->count(5)->create();
        }

        // Create a few suppliers to reuse
        $suppliers = Supplier::factory()->count(5)->create([
            'user_id' => $user->id,
            'farm_id' => $farms->first()->id
        ]);

        foreach ($farms as $farm) {
            // Check if farm already has animals to avoid redundant seeding
            if (Animal::where('farm_id', $farm->id)->exists()) {
                continue;
            }

            // Create a herd for farm
            $num = 20;
            Animal::factory()->count($num)->create([
                'farm_id' => $farm->id,
                'breed_id' => $breeds->random()->id,
                'user_id' => $user->id,
                'supplier_id' => $suppliers->random()->id,
            ]);
        }
    }
}
