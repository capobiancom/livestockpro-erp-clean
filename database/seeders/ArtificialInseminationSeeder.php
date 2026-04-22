<?php

namespace Database\Seeders;

use App\Models\ArtificialInsemination;
use App\Models\Farm;
use App\Models\User;
use App\Models\Breed;
use App\Models\Animal;
use Illuminate\Database\Seeder;

class ArtificialInseminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $farm = Farm::first();
        $breed = Breed::first();
        $animal = Animal::first();

        ArtificialInsemination::factory()->count(10)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'breed_id' => $breed->id,
            'vet_id' => $user->id,
            'reproduction_record_id' => function() use ($user, $farm, $animal) {
                return \App\Models\ReproductionRecord::factory()->create([
                    'user_id' => $user->id,
                    'farm_id' => $farm->id,
                    'animal_id' => $animal->id,
                    'partner_id' => $animal->id,
                ]);
            },
        ]);
    }
}
