<?php

namespace Database\Seeders;

use App\Models\PurchaseItem;
use Illuminate\Database\Seeder;

class PurchaseItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PurchaseItem::factory()->count(50)->create();
    }
}
