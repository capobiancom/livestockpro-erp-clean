<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Herd;
use App\Models\Farm; // Import Farm model
use App\Models\User;

class HerdSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure there are farms to associate with herds
        if (Farm::count() === 0) {
            // If no farms exist, create one or handle this case as appropriate
            // For now, we'll assume FarmSeeder runs before this or create a dummy farm
            Farm::factory()->create();
        }

        // Get a random farm ID
        $farmId = Farm::all()->random()->id;

        $user = User::first();
        $userId = $user ? $user->id : null;

        Herd::factory()->count(10)->create([
            'farm_id' => $farmId,
            'user_id' => $userId,
        ]);
    }
}
