<?php

namespace Database\Seeders;

use App\Models\Calf;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CalfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are farms and animals to associate with calves
        if (Farm::count() === 0) {
            \App\Models\Farm::factory()->create();
        }
        if (Animal::count() === 0) {
            \App\Models\Animal::factory()->count(10)->create();
        }

        $user = User::first();
        Calf::factory()->count(50)->create([
            'user_id' => $user->id,
        ]);
    }
}
