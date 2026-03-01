<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MilkSale;
use App\Models\User;
use App\Models\Farm;
use App\Models\Supplier;

class MilkSaleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $customer = Supplier::first();

        MilkSale::factory()->count(30)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'customer_id' => $customer->id,
        ]);
    }
}
