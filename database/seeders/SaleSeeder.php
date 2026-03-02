<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\Supplier;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $animal = Animal::first();
        $customer = Supplier::first();

        $userId = $user ? $user->id : null;
        $farmId = $farm ? $farm->id : null;
        $animalId = $animal ? $animal->id : null;
        $customerId = $customer ? $customer->id : null;

        Sale::factory()->count(10)->create([
            'user_id'     => $userId,
            'farm_id'     => $farmId,
            'animal_id'   => $animalId,
            'customer_id' => $customerId,
        ]);
    }
}
