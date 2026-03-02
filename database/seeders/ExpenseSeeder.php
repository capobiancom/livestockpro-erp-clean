<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Farm; // Import Farm model
use App\Models\User;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $farms = Farm::all();

        if ($farms->isEmpty()) {
            $farms = Farm::factory()->count(3)->create();
        }

        $user = User::first();
        $userId = $user ? $user->id : null;

        foreach ($farms as $farm) {
            Expense::factory()->count(30)->create([
                'farm_id' => $farm->id,
                'user_id' => $userId,
            ]);
        }
    }
}
