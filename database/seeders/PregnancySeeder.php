<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class PregnancySeeder extends Seeder
{
    public function run(): void
    {
        // prefer a farm user so records will be visible on owner reports
        $user = User::whereNotNull('farm_id')->first() ?? User::first();

        // Seed against the same farm as the selected user (so reports scoped by session farm_id/user farm_id can see it).
        // Fallback to first farm only if user has no farm_id.
        $farm = $user?->farm_id ? Farm::find($user->farm_id) : Farm::first();
        if (!$user || !$farm) {
            return;
        }

        // Link confirmed pregnancies to a subset of reproduction records
        // so the report shows a realistic success rate.
        // Only use attempts from the same farm; otherwise the report will not find matches after scoping.
        $attempts = ReproductionRecord::query()
            ->when($farm?->id, fn($q) => $q->where('farm_id', $farm->id))
            ->orderByDesc('event_date')
            ->limit(60)
            ->get();

        if ($attempts->isEmpty()) {
            return;
        }

        // ~45% confirmed rate (adjust by changing the take() number)
        $confirmedAttempts = $attempts->shuffle()->take((int) round($attempts->count() * 0.45));

        foreach ($confirmedAttempts as $attempt) {
            $confirmedDate = $attempt->event_date
                ? $attempt->event_date->copy()->addDays(random_int(25, 45))
                : now()->subDays(random_int(1, 60));

            Pregnancy::factory()->create([
                // Prefer linking the pregnancy to the same user who created the attempt (if present),
                // so "logged-in user" scoping doesn't hide it.
                'user_id' => $attempt->user_id ?? $user->id,
                'farm_id' => $farm->id,
                'animal_id' => $attempt->animal_id,
                'reproduction_record_id' => $attempt->id,

                // ensure it counts as "confirmed" for the report
                'pregnancy_confirmed_date' => $confirmedDate,

                // expected calving is required (NOT NULL in DB schema)
                'expected_calving_date' => $confirmedDate->copy()->addDays(280),
                'expected_gestation_days' => 280,

                // keep status compatible with enum cast
                'pregnancy_status' => 'confirmed',
            ]);
        }
    }
}
