<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeShift;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\User;
use App\Models\Farm;

class EmployeeShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $farms = Farm::all();
        $employees = Employee::all();
        $shifts = Shift::all();

        if ($users->isEmpty() || $farms->isEmpty() || $employees->isEmpty() || $shifts->isEmpty()) {
            echo "Skipping EmployeeShiftSeeder: No users, farms, employees, or shifts found.\n";
            return;
        }

        foreach ($users as $user) {
            foreach ($farms as $farm) {
                // Get employees and shifts belonging to the current farm
                $farmEmployees = $employees->where('farm_id', $farm->id);
                $farmShifts = $shifts->where('farm_id', $farm->id);

                if ($farmEmployees->isEmpty() || $farmShifts->isEmpty()) {
                    continue; // Skip if no employees or shifts for this farm
                }

                foreach ($farmEmployees as $employee) {
                    // Assign a random shift to each employee for the current farm
                    $randomShift = $farmShifts->random();

                    EmployeeShift::factory()->create([
                        'employee_id' => $employee->id,
                        'shift_id' => $randomShift->id,
                        'user_id' => $user->id,
                        'farm_id' => $farm->id,
                        'effective_from' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                        'effective_to' => $this->faker->optional(0.7)->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                    ]);
                }
            }
        }
    }
}
