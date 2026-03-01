<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\User;
use App\Models\Farm;
use App\Models\MedicineGroup;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $farm = Farm::first();
        $medicineGroup = MedicineGroup::first();
        $supplier = Supplier::first();
        $category = Category::first();

        Medicine::factory()->count(10)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'medicine_group_id' => $medicineGroup->id,
            'supplier_id' => $supplier->id,
            'inventory_category_id' => $category->id,
        ]);
    }
}
