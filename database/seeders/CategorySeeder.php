<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farm = Farm::first();

        if (!$farm) {
            $farm = Farm::factory()->create();
        }

        $user = User::first();
        Category::factory()->count(5)->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
        ]);
    }
}
