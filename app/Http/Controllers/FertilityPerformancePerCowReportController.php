<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\CalvingRecord;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class FertilityPerformancePerCowReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('fertilityPerformancePerCowReport', Pregnancy::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'service_type' => ['nullable', 'string', 'in:all,ai,natural_mating,embryo_transfer'],
            'performance' => ['nullable', 'string', 'in:all,excellent,moderate,poor'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(365)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $serviceType = $validated['service_type'] ?? 'all';
        $performance = $validated['performance'] ?? 'all';

        $animals = Animal::query()
            ->select(['id', 'tag_number', 'name'])
            ->orderBy('tag_number')
            ->limit(500)
            ->get();

        $attemptsQuery = ReproductionRecord::query()
            ->select(['id', 'animal_id', 'event', 'event_date'])
            ->whereBetween('event_date', [$from, $to]);

        if (!empty($validated['animal_id'])) {
            $attemptsQuery->where('animal_id', $validated['animal_id']);
        }

        if ($serviceType !== 'all') {
            $attemptsQuery->whereIn('event', $this->eventsForServiceType($serviceType));
        } else {
            $attemptsQuery->whereIn('event', $this->eventsForServiceType('all'));
        }

        $attempts = $attemptsQuery
            ->orderBy('event_date')
            ->limit(50000)
            ->get();

        $attemptIds = $attempts->pluck('id')->all();
        $animalIds = $attempts->pluck('animal_id')->unique()->values()->all();

        if (!empty($validated['animal_id']) && !in_array((int) $validated['animal_id'], $animalIds, true)) {
            $animalIds[] = (int) $validated['animal_id'];
        }

        $confirmedPregnancies = Pregnancy::query()
            ->select(['id', 'animal_id', 'reproduction_record_id', 'pregnancy_confirmed_date', 'pregnancy_status'])
            ->whereIn('animal_id', $animalIds)
            ->whereIn('reproduction_record_id', $attemptIds)
            ->whereNotNull('pregnancy_confirmed_date')
            ->get();

        $confirmedByAttemptId = $confirmedPregnancies->keyBy('reproduction_record_id');

        $lossPregnancies = Pregnancy::query()
            ->select(['id', 'animal_id', 'reproduction_record_id', 'pregnancy_confirmed_date', 'pregnancy_status'])
            ->whereIn('animal_id', $animalIds)
            ->whereIn('reproduction_record_id', $attemptIds)
            ->whereNotNull('pregnancy_confirmed_date')
            ->whereIn('pregnancy_status', $this->lossStatuses())
            ->get();

        $lossByAttemptId = $lossPregnancies->keyBy('reproduction_record_id');

        $calvings = CalvingRecord::query()
            ->select(['id', 'pregnancy_id', 'calving_date'])
            ->with(['pregnancy:id,animal_id'])
            ->whereBetween('calving_date', [$from, $to])
            ->orderBy('calving_date')
            ->limit(50000)
            ->get()
            ->filter(fn($r) => $r->pregnancy && $r->pregnancy->animal_id)
            ->values();

        $calvingsByAnimal = $calvings->groupBy(fn($r) => (int) $r->pregnancy->animal_id);

        $attemptsByAnimal = $attempts->groupBy(fn($a) => (int) $a->animal_id);

        $rows = collect();

        foreach ($animalIds as $animalId) {
            $animalId = (int) $animalId;

            $animal = $animals->firstWhere('id', $animalId);

            $animalAttempts = ($attemptsByAnimal[$animalId] ?? collect())
                ->sortBy('event_date')
                ->values();

            $totalServices = $animalAttempts->count();

            $confirmedCount = $animalAttempts->filter(fn($a) => $confirmedByAttemptId->has($a->id))->count();

            $spc = $confirmedCount > 0 ? round($totalServices / $confirmedCount, 2) : null;

            $conceptionRate = $totalServices > 0 ? round(($confirmedCount / $totalServices) * 100, 2) : 0;

            $lossCount = $animalAttempts->filter(fn($a) => $lossByAttemptId->has($a->id))->count();
            $pregLossRate = $confirmedCount > 0 ? round(($lossCount / $confirmedCount) * 100, 2) : 0;

            $daysOpenValues = $this->computeDaysOpenValues($animalId, $calvingsByAnimal, $confirmedPregnancies);
            $avgDaysOpen = count($daysOpenValues) > 0 ? round(array_sum($daysOpenValues) / count($daysOpenValues), 2) : null;

            $calvingIntervalValues = $this->computeCalvingIntervalValues($animalId, $calvingsByAnimal);
            $avgCalvingInterval = count($calvingIntervalValues) > 0 ? round(array_sum($calvingIntervalValues) / count($calvingIntervalValues), 2) : null;

            $score = $this->fertilityScore([
                'conception_rate' => $conceptionRate,
                'spc' => $spc,
                'avg_days_open' => $avgDaysOpen,
                'preg_loss_rate' => $pregLossRate,
            ]);

            $bucket = $this->scoreBucket($score);

            if ($performance !== 'all' && $bucket !== $performance) {
                continue;
            }

            $rows->push([
                'animal_id' => $animalId,
                'tag_number' => $animal?->tag_number,
                'animal_name' => $animal?->name,
                'total_services' => $totalServices,
                'confirmed_pregnancies' => $confirmedCount,
                'conception_rate' => $conceptionRate,
                'spc' => $spc,
                'avg_days_open' => $avgDaysOpen,
                'avg_calving_interval_days' => $avgCalvingInterval,
                'pregnancy_losses' => $lossCount,
                'pregnancy_loss_rate' => $pregLossRate,
                'fertility_score' => $score,
                'performance' => $bucket,
                'performance_label' => $this->scoreLabel($bucket),
            ]);
        }

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'cows' => $rows->count(),
            'avg_conception_rate' => $rows->count() > 0 ? round($rows->avg('conception_rate'), 2) : 0,
            'avg_spc' => $rows->whereNotNull('spc')->count() > 0 ? round($rows->whereNotNull('spc')->avg('spc'), 2) : null,
            'avg_days_open' => $rows->whereNotNull('avg_days_open')->count() > 0 ? round($rows->whereNotNull('avg_days_open')->avg('avg_days_open'), 2) : null,
            'avg_calving_interval_days' => $rows->whereNotNull('avg_calving_interval_days')->count() > 0 ? round($rows->whereNotNull('avg_calving_interval_days')->avg('avg_calving_interval_days'), 2) : null,
        ];

        return Inertia::render('Reports/FertilityPerformancePerCowReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'service_type' => $serviceType,
                'performance' => $performance,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $rows
                ->sortBy([
                    ['fertility_score', 'desc'],
                    ['conception_rate', 'desc'],
                ])
                ->values(),
        ]);
    }

    private function computeDaysOpenValues(int $animalId, $calvingsByAnimal, $confirmedPregnancies): array
    {
        $calvings = ($calvingsByAnimal[$animalId] ?? collect())
            ->sortBy('calving_date')
            ->values();

        if ($calvings->count() === 0) {
            return [];
        }

        $pregs = $confirmedPregnancies
            ->where('animal_id', $animalId)
            ->filter(fn($p) => !empty($p->pregnancy_confirmed_date))
            ->sortBy('pregnancy_confirmed_date')
            ->values();

        if ($pregs->count() === 0) {
            return [];
        }

        $values = [];

        foreach ($calvings as $calving) {
            $calvingDate = $calving->calving_date ? Carbon::parse($calving->calving_date) : null;
            if (!$calvingDate) {
                continue;
            }

            $nextPreg = $pregs->first(function ($p) use ($calvingDate) {
                $d = $p->pregnancy_confirmed_date ? Carbon::parse($p->pregnancy_confirmed_date) : null;
                return $d && $d->greaterThan($calvingDate);
            });

            if (!$nextPreg) {
                continue;
            }

            $confirmedDate = Carbon::parse($nextPreg->pregnancy_confirmed_date);
            $values[] = $calvingDate->diffInDays($confirmedDate);
        }

        return $values;
    }

    private function computeCalvingIntervalValues(int $animalId, $calvingsByAnimal): array
    {
        $calvings = ($calvingsByAnimal[$animalId] ?? collect())
            ->sortBy('calving_date')
            ->values();

        if ($calvings->count() < 2) {
            return [];
        }

        $values = [];

        for ($i = 1; $i < $calvings->count(); $i++) {
            $prev = $calvings[$i - 1];
            $curr = $calvings[$i];

            $prevDate = $prev->calving_date ? Carbon::parse($prev->calving_date) : null;
            $currDate = $curr->calving_date ? Carbon::parse($curr->calving_date) : null;

            if (!$prevDate || !$currDate) {
                continue;
            }

            $values[] = $prevDate->diffInDays($currDate);
        }

        return $values;
    }

    private function fertilityScore(array $m): float
    {
        // Weighted index (0..100-ish). We normalize each metric to 0..100.
        $conception = $this->clamp((float) ($m['conception_rate'] ?? 0), 0, 100);

        $spcScore = $this->spcScore($m['spc'] ?? null);
        $daysOpenScore = $this->daysOpenScore($m['avg_days_open'] ?? null);
        $lossScore = $this->pregLossScore($m['preg_loss_rate'] ?? 0);

        $score = ($conception * 0.4)
            + ($spcScore * 0.2)
            + ($daysOpenScore * 0.2)
            + ($lossScore * 0.2);

        return round($score, 2);
    }

    private function spcScore($spc): float
    {
        // Ideal 1.5–2.0. Lower is better.
        if ($spc === null) {
            return 50; // neutral when unknown
        }

        $spc = (float) $spc;

        if ($spc <= 1.5) {
            return 100;
        }

        if ($spc <= 2.0) {
            return 85;
        }

        if ($spc <= 3.0) {
            return 60;
        }

        if ($spc <= 4.0) {
            return 40;
        }

        return 20;
    }

    private function daysOpenScore($daysOpen): float
    {
        // Ideal < 115 days.
        if ($daysOpen === null) {
            return 50;
        }

        $d = (float) $daysOpen;

        if ($d <= 85) {
            return 100;
        }

        if ($d <= 115) {
            return 85;
        }

        if ($d <= 140) {
            return 60;
        }

        if ($d <= 170) {
            return 40;
        }

        return 20;
    }

    private function pregLossScore(float $lossRate): float
    {
        // Lower is better.
        if ($lossRate <= 5) {
            return 100;
        }
        if ($lossRate <= 10) {
            return 80;
        }
        if ($lossRate <= 20) {
            return 60;
        }
        if ($lossRate <= 30) {
            return 40;
        }
        return 20;
    }

    private function scoreBucket(float $score): string
    {
        if ($score >= 75) {
            return 'excellent';
        }
        if ($score >= 55) {
            return 'moderate';
        }
        return 'poor';
    }

    private function scoreLabel(string $bucket): string
    {
        return match ($bucket) {
            'excellent' => 'Excellent',
            'moderate' => 'Moderate',
            'poor' => 'Poor',
            default => 'All',
        };
    }

    private function lossStatuses(): array
    {
        // Best-effort mapping. Adjust if your enum values differ.
        return [
            'aborted',
            'abortion',
            'lost',
            'loss',
            'miscarriage',
            'terminated',
            'failed',
        ];
    }

    private function eventsForServiceType(string $serviceType): array
    {
        // Best-effort mappings based on ReproductionRecord.event.
        $ai = ['ai', 'artificial_insemination', 'artificial insemination', 'Artificial Insemination'];
        $natural = ['natural_mating', 'natural mating', 'mating', 'Natural Mating'];
        $embryo = ['embryo_transfer', 'embryo transfer', 'Embryo Transfer'];

        return match ($serviceType) {
            'ai' => $ai,
            'natural_mating' => $natural,
            'embryo_transfer' => $embryo,
            default => array_values(array_unique(array_merge($ai, $natural, $embryo))),
        };
    }

    private function clamp(float $v, float $min, float $max): float
    {
        return max($min, min($max, $v));
    }
}
