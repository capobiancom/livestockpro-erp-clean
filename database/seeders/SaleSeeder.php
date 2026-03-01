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

        Sale::factory()->count(10)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'animal_id' => $animal->id,
            'customer_id' => $customer->id,
        ]);
    }
}
