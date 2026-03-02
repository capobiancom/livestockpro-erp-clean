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

        $userId = $user ? $user->id : null;
        $farmId = $farm ? $farm->id : null;
        $customerId = $customer ? $customer->id : null;

        MilkSale::factory()->count(30)->create([
            'user_id'     => $userId,
            'farm_id'     => $farmId,
            'customer_id' => $customerId,
        ]);
    }
}
