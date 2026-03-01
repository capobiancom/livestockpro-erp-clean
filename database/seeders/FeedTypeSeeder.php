<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeedType;
use App\Models\User;
use App\Models\Farm;

class FeedTypeSeeder extends Seeder
{
    public function run(): void
    {
        $common = [
            ['name' => 'Concentrate', 'unit' => 'kg'],
            ['name' => 'Hay', 'unit' => 'kg'],
            ['name' => 'Silage', 'unit' => 'kg'],
            ['name' => 'Mineral Mix', 'unit' => 'kg'],
            ['name' => 'Milk Replacer', 'unit' => 'liter'],
        ];

        $user = User::first();
        $farm = Farm::first();

        foreach ($common as $c) {
            FeedType::create(array_merge($c, [
                'user_id' => $user->id,
                'farm_id' => $farm->id,
            ]));
        }

        FeedType::factory()->count(5)->create([
            'user_id' => $user->id,
            'farm_id' => $farm->id,
        ]);
    }
}
