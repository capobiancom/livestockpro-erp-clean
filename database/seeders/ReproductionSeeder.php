<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\ArtificialInsemination;
use App\Models\Farm;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReproductionSeeder extends Seeder
{
    public function run(): void
    {
        // choose a user with a farm_id if possible (farm owner); else fallback to
        // the very first user
        $user = User::whereNotNull('farm_id')->first() ?? User::first();

        // Seed against the same farm as the selected user (so reports scoped by session farm_id/user farm_id can see it).
        // Fallback to first farm only if user has no farm_id.
        $farm = $user?->farm_id ? Farm::find($user->farm_id) : Farm::first();

        if (!$user || !$farm) {
            return;
        }

        // Use multiple animals if available so animal filter is meaningful.
        // Use animals from the same farm; otherwise the report's farm filter will hide them.
        $animals = Animal::query()
            ->when($farm?->id, fn($q) => $q->where('farm_id', $farm->id))
            ->limit(8)
            ->get();
        if ($animals->isEmpty()) {
            return;
        }

        $aiId = ArtificialInsemination::query()->value('id');

        $events = [
            'ai' => ['ai', 'artificial_insemination', 'Artificial Insemination'],
            'natural_mating' => ['natural_mating', 'Natural Mating', 'mating'],
            'embryo_transfer' => ['embryo_transfer', 'Embryo Transfer'],
        ];

        // Create attempts across the last ~90 days so date filters show data.
        $start = now()->subDays(90)->startOfDay();

        foreach (range(1, 60) as $i) {
            $animal = $animals->random();

            $bucket = collect(array_keys($events))->random();
            $event = collect($events[$bucket])->random();

            $date = Carbon::parse($start)->addDays(random_int(0, 89))->addHours(random_int(0, 23));

            ReproductionRecord::factory()->create([
                'user_id' => $user->id,
                'farm_id' => $farm->id,
                'animal_id' => $animal->id,
                'partner_id' => $animal->id,
                'artificial_insemination_id' => $aiId,
                'event' => $event,
                'event_date' => $date,
            ]);
        }
    }
}
