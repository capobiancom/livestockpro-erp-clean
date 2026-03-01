<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Farm;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();

        \App\Models\EventType::factory()->count(6)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
    }
}
