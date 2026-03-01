<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Farm;
use App\Models\Category;
use App\Models\Supplier;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $farm = Farm::first();
        $category = Category::first();
        $supplier = Supplier::first();

        InventoryItem::factory()->count(10)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'inventory_category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);
    }
}
