<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Farm;
use App\Models\Supplier;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $supplier = Supplier::first();

        Purchase::factory()->count(30)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'supplier_id' => $supplier->id,
        ]);
    }
}
