<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Category;
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

        if ($farm) {
            Category::factory()->count(5)->create([
                'farm_id' => $farm->id,
            ]);
        } else {
            // Handle the case where no farm exists, e.g., create a default one
            $farm = Farm::factory()->create();
            Category::factory()->count(5)->create([
                'farm_id' => $farm->id,
            ]);
        }
    }
}
