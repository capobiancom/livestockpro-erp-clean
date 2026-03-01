<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Calf;
use App\Models\CalvingRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class CalvingIntervalReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('calvingIntervalReport', Calf::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'performance' => ['nullable', 'string', 'in:all,excellent,good,poor'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(365)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $performance = $validated['performance'] ?? 'all';

        $animals = Animal::query()
            ->select(['id', 'tag_number', 'name'])
            ->orderBy('tag_number')
            ->limit(500)
            ->get();

        // We compute CI per cow by ordering calving records and taking the diff
        // between consecutive calving dates.
        $recordsQuery = CalvingRecord::query()
            ->select(['id', 'farm_id', 'pregnancy_id', 'calving_date'])
            ->with([
                'pregnancy:id,animal_id',
                'pregnancy.animal:id,tag_number,name',
            ])
            ->whereBetween('calving_date', [$from, $to])
            ->orderBy('calving_date');

        if (!empty($validated['animal_id'])) {
            $recordsQuery->whereHas('pregnancy', function ($q) use ($validated) {
                $q->where('animal_id', $validated['animal_id']);
            });
        }

        $records = $recordsQuery
            ->limit(20000)
            ->get();

        $byAnimal = $records
            ->filter(fn($r) => $r->pregnancy && $r->pregnancy->animal)
            ->groupBy(fn($r) => $r->pregnancy->animal_id);

        $rows = collect();

        foreach ($byAnimal as $animalId => $items) {
            $sorted = $items->sortBy('calving_date')->values();

            for ($i = 1; $i < $sorted->count(); $i++) {
                $prev = $sorted[$i - 1];
                $curr = $sorted[$i];

                $prevDate = $prev->calving_date ? Carbon::parse($prev->calving_date) : null;
                $currDate = $curr->calving_date ? Carbon::parse($curr->calving_date) : null;

                if (!$prevDate || !$currDate) {
                    continue;
                }

                $ciDays = $prevDate->diffInDays($currDate);

                $bucket = $this->performanceBucket($ciDays);

                if ($performance !== 'all' && $bucket !== $performance) {
                    continue;
                }

                $animal = $curr->pregnancy->animal;

                $rows->push([
                    'animal_id' => (int) $animalId,
                    'tag_number' => $animal->tag_number,
                    'animal_name' => $animal->name,
                    'previous_calving_date' => $prevDate->toDateString(),
                    'current_calving_date' => $currDate->toDateString(),
                    'calving_interval_days' => $ciDays,
                    'performance' => $bucket,
                    'performance_label' => $this->performanceLabel($bucket),
                ]);
            }
        }

        $totalIntervals = $rows->count();
        $avgCi = $totalIntervals > 0 ? round($rows->avg('calving_interval_days'), 2) : 0;
        $minCi = $totalIntervals > 0 ? (int) $rows->min('calving_interval_days') : 0;
        $maxCi = $totalIntervals > 0 ? (int) $rows->max('calving_interval_days') : 0;

        $counts = [
            'excellent' => $rows->where('performance', 'excellent')->count(),
            'good' => $rows->where('performance', 'good')->count(),
            'poor' => $rows->where('performance', 'poor')->count(),
        ];

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_intervals' => $totalIntervals,
            'average_ci_days' => $avgCi,
            'min_ci_days' => $minCi,
            'max_ci_days' => $maxCi,
            'excellent_count' => $counts['excellent'],
            'good_count' => $counts['good'],
            'poor_count' => $counts['poor'],
        ];

        return Inertia::render('Reports/CalvingIntervalReports/Index', [
            'filters' => [
                'from' => $from,
                'to' => $to,
                'animal_id' => $validated['animal_id'] ?? null,
                'performance' => $performance,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $rows
                ->sortByDesc('calving_interval_days')
                ->values(),
        ]);
    }

    private function performanceBucket(int $ciDays): string
    {
        // Industry targets (dairy):
        // Excellent: 365 days
        // Good: 365–400 days
        // Poor: > 420 days
        //
        // We treat:
        // - <= 365 as excellent
        // - 366..400 as good
        // - 401..420 as "good" (still acceptable-ish) OR "poor"?
        // The user specified "Poor > 420", so 401..420 is neither.
        // We'll classify 401..420 as "good" to avoid an "unclassified" bucket.
        if ($ciDays <= 365) {
            return 'excellent';
        }

        if ($ciDays <= 420) {
            return 'good';
        }

        return 'poor';
    }

    private function performanceLabel(string $bucket): string
    {
        return match ($bucket) {
            'excellent' => 'Excellent (≤ 365 days)',
            'good' => 'Good (366–420 days)',
            'poor' => 'Poor (> 420 days)',
            default => 'All',
        };
    }
}
