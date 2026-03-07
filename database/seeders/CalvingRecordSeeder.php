<?php

namespace Database\Seeders;

use App\Models\CalvingRecord;
use App\Models\Farm;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CalvingRecordSeeder extends Seeder
{
    public function run(): void
    {
        // Prefer farm user so seeded data shows for owner-scoped reports.
        $user = User::whereNotNull('farm_id')->first() ?? User::first();
        $farm = $user?->farm_id ? Farm::find($user->farm_id) : Farm::first();

        if (!$user || !$farm) {
            return;
        }

        // This report groups calving records by pregnancy->animal and needs
        // at least 2 calving records per animal to produce intervals.
        $pregnancies = Pregnancy::query()
            ->where('farm_id', $farm->id)
            ->with(['animal:id,farm_id'])
            ->limit(60)
            ->get()
            ->filter(fn($p) => (bool) $p->animal);

        if ($pregnancies->isEmpty()) {
            return;
        }

        // Pick a subset of animals and create 2-3 calving records per animal
        // spaced by 330-460 days to produce excellent/good/poor buckets.
        $byAnimal = $pregnancies->groupBy('animal_id')->take(6);

        $now = now()->startOfDay();

        foreach ($byAnimal as $animalId => $items) {
            // Ensure deterministic order and pick up to 3 pregnancies as anchors
            $items = $items->sortByDesc('pregnancy_confirmed_date')->values();

            // We'll use the first pregnancy as "current", and attach multiple
            // calving records to different pregnancies if possible.
            $targets = $items->take(3)->values();

            // Base calving date is within last 120 days (so default report window shows data)
            $calving3 = Carbon::parse($now)->subDays(random_int(10, 120));

            // Build previous calvings to create intervals
            $gap2 = random_int(330, 460); // excellent/good/poor spread
            $gap1 = random_int(330, 460);

            $calving2 = (clone $calving3)->subDays($gap2);
            $calving1 = (clone $calving2)->subDays($gap1);

            $dates = collect([$calving1, $calving2, $calving3]);

            // Use up to 3 pregnancies (fallback to reusing one pregnancy_id if needed)
            foreach ($dates as $idx => $date) {
                $pregnancy = $targets[$idx] ?? $targets->first();

                // Avoid duplicates if seeder is re-run
                $exists = CalvingRecord::query()
                    ->where('farm_id', $farm->id)
                    ->where('pregnancy_id', $pregnancy->id)
                    ->whereDate('calving_date', $date->toDateString())
                    ->exists();

                if ($exists) {
                    continue;
                }

                CalvingRecord::create([
                    'farm_id' => $farm->id,
                    'user_id' => $user->id,
                    'pregnancy_id' => $pregnancy->id,
                    'calving_date' => $date->toDateString(),

                    // Keep values compatible with enum casts
                    'calving_type' => 'normal',
                    'calves_count' => 1,
                    'calf_gender' => 'female',
                    'calving_outcome' => 'successful',
                    'notes' => 'Seeded calving record for calving interval report',
                ]);
            }
        }
    }
}
