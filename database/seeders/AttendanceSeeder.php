<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Farm;
use App\Models\StaffProfile;
use Illuminate\Foundation\Testing\WithFaker;

class AttendanceSeeder extends Seeder
{
    use WithFaker;

    public function run(): void
    {
        $this->setUpFaker(); // Initialize faker
        $user = User::first();
        $farm = Farm::first();

        // Create 50 unique attendance records
        for ($i = 0; $i < 50; $i++) {
            $staff = StaffProfile::factory()->create([
                'user_id' => $user->id,
                'farm_id' => $farm->id,
            ]);

            Attendance::factory()->create([
                'user_id' => $user->id,
                'farm_id' => $farm->id,
                'staff_id' => $staff->id,
                'date' => $this->faker->unique()->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
