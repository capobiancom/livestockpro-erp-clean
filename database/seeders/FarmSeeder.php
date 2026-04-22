<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as FakerFactory; // Import Faker Factory

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        // Reset Faker's unique generator to prevent unique constraint violations
        FakerFactory::create()->unique(true);

        // If the users table doesn't yet exist (during early install steps)
        // avoid querying it or the seeder will throw a "table not found" error.
        $userId = null;
        try {
            if (Schema::hasTable('users')) {
                $user = User::first();
                $userId = $user ? $user->id : null;
            }
        } catch (\Throwable $e) {
            // swallowing any DB error since installation may not have created tables
        }

        Farm::factory()->count(5)->create([
            // if no user exists yet (clean install), allow the foreign key to be null
            'user_id' => $userId,
        ]);
    }
}
