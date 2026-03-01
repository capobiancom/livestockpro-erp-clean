<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MilkRecord;
use App\Models\User;
use App\Models\Farm;
use App\Models\Animal;
use App\Models\StaffProfile;
use Illuminate\Foundation\Testing\WithFaker;

class MilkRecordSeeder extends Seeder
{
    use WithFaker;

    public function run(): void
    {
        $this->setUpFaker(); // Initialize faker
        $user = User::first();
        $farm = Farm::first();
        $staff = StaffProfile::first();

        // Create 50 unique milk records
        for ($i = 0; $i < 50; $i++) {
            $animal = Animal::factory()->create([
                'user_id' => $user->id,
                'farm_id' => $farm->id,
            ]);
            MilkRecord::factory()->create([
                'user_id' => $user->id,
                'farm_id' => $farm->id,
                'animal_id' => $animal->id,
                'staff_id' => $staff->id,
                'date' => $this->faker->unique()->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
