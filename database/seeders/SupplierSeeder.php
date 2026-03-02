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

        $userId = $user ? $user->id : null;
        $farmId = $farm ? $farm->id : null;

        Supplier::factory()->count(10)->create([
            'user_id' => $userId,
            'farm_id' => $farmId,
        ]);
    }
}
