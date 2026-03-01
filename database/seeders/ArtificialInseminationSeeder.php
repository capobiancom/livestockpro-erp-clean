<?php

namespace Database\Seeders;

use App\Models\ArtificialInsemination;
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
        ArtificialInsemination::factory()->count(10)->create();
    }
}
