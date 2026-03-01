<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReproductionRecord;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\ArtificialInsemination;

class ReproductionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $animal = Animal::first();
        $artificialInsemination = ArtificialInsemination::first();

        ReproductionRecord::factory()->count(20)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'partner_id' => $animal->id, // Assuming partner can be the same animal for seeding purposes
            'artificial_insemination_id' => $artificialInsemination->id,
        ]);
    }
}
