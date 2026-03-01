<?php

namespace Database\Factories;

use App\Models\DiseaseTreatment;
use App\Models\DiseaseTreatmentMedication;
use App\Models\Farm;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiseaseTreatmentMedication>
 */
class DiseaseTreatmentMedicationFactory extends Factory
{
    protected $model = DiseaseTreatmentMedication::class;

    public function definition(): array
    {
        $qty = $this->faker->randomFloat(2, 1, 10);
        $unitCost = $this->faker->randomFloat(2, 5, 200);

        return [
            'disease_treatment_id' => DiseaseTreatment::factory(),
            'medicine_id' => Medicine::factory(),
            'farm_id' => Farm::factory(),
            'dose' => $this->faker->randomElement(['5ml', '10ml', '1 tab', '2 tab']),
            'frequency' => $this->faker->randomElement(['once daily', 'twice daily', 'every 8 hours']),
            'duration_days' => $this->faker->numberBetween(1, 14),
            'status' => $this->faker->randomElement(['planned', 'ongoing', 'completed']),
            'started_at' => $this->faker->optional()->date(),
            'ended_at' => $this->faker->optional()->date(),
            'qty' => $qty,
            'unit_cost' => $unitCost,
            'total_cost' => round($qty * $unitCost, 2),
            'notes' => $this->faker->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
