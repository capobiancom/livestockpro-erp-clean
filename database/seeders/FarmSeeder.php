<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\User;
use Faker\Factory as FakerFactory; // Import Faker Factory

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        // Reset Faker's unique generator to prevent unique constraint violations
        FakerFactory::create()->unique(true);

        $user = User::first();
        Farm::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
    }
}
