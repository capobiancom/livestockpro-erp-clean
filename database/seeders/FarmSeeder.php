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
        $userId = $user ? $user->id : null;

        Farm::factory()->count(5)->create([
            // if no user exists yet (clean install), allow the foreign key to be null
            'user_id' => $userId,
        ]);
    }
}
