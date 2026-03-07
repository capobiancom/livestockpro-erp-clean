<?php

namespace Database\Factories;

use App\Models\Farm;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pregnancy>
 */
class PregnancyFactory extends Factory
{
    protected $model = Pregnancy::class;

    public function definition(): array
    {
        $confirmedAt = $this->faker->optional(0.75)->dateTimeBetween('-60 days', 'now');

        return [
            'user_id' => User::query()->inRandomOrder()->value('id'),
            'farm_id' => Farm::query()->inRandomOrder()->value('id'),
            'animal_id' => null, // usually set by seeder when linking to a reproduction record
            'reproduction_record_id' => ReproductionRecord::query()->inRandomOrder()->value('id'),

            'pregnancy_confirmed_date' => $confirmedAt ? $confirmedAt->format('Y-m-d') : null,

            'expected_gestation_days' => $this->faker->randomElement([270, 275, 280, 283]),
            'expected_calving_date' => $confirmedAt
                ? \Illuminate\Support\Carbon::parse($confirmedAt)->addDays(280)->toDateString()
                : null,

            // Must match App\Enums\PregnancyStatus backing values.
            'pregnancy_status' => $this->faker->randomElement([
                'ongoing',
                'confirmed',
                'completed',
                'aborted',
                'abortion',
                'embryonic_death',
                'miscarriage',
                'lost',
                'pregnancy_loss',
            ]),

            'health_notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
