<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Farm;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        Supplier::factory()->count(10)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
    }
}
