<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Pregnancy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class PregnancyLossAnalysisReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('pregnancyLossAnalysis', Pregnancy::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'group_by' => ['nullable', 'string', 'in:loss_timing,month'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $groupBy = $validated['group_by'] ?? 'loss_timing';

        $animals = Animal::query()
            ->select(['id', 'tag', 'name'])
            ->orderBy('tag')
            ->limit(500)
            ->get();

        // Confirmed pregnancies within range (by confirmed date).
        $confirmedQuery = Pregnancy::query()
            ->select([
                'id',
                'animal_id',
                'pregnancy_confirmed_date',
                'pregnancy_status',
                'abortion_date',
                'embryonic_death_date',
                'miscarriage_date',
                'calving_date',
            ])
            ->whereNotNull('pregnancy_confirmed_date')
            ->whereBetween('pregnancy_confirmed_date', [$from, $to]);

        if (!empty($validated['animal_id'])) {
            $confirmedQuery->where('animal_id', $validated['animal_id']);
        }

        $confirmed = $confirmedQuery
            ->orderByDesc('pregnancy_confirmed_date')
            ->limit(5000)
            ->get();

        $rows = $confirmed->map(function (Pregnancy $p) {
            $lossDate = $this->lossDate($p);
            $lossType = $this->lossType($p);

            $timing = null;
            $daysFromConfirm = null;

            if ($lossDate && $p->pregnancy_confirmed_date) {
                $daysFromConfirm = Carbon::parse($p->pregnancy_confirmed_date)->diffInDays(Carbon::parse($lossDate));
                $timing = $this->lossTimingBucket($daysFromConfirm);
            }

            return [
                'pregnancy_id' => $p->id,
                'animal_id' => $p->animal_id,
                'confirmed_date' => optional($p->pregnancy_confirmed_date)->toDateString(),
                'status' => $p->pregnancy_status?->value ?? (string) $p->pregnancy_status,
                'loss' => $lossDate !== null,
                'loss_type' => $lossType,
                'loss_date' => $lossDate ? Carbon::parse($lossDate)->toDateString() : null,
                'days_from_confirm' => $daysFromConfirm,
                'loss_timing' => $timing,
                'calving_date' => $p->calving_date ? Carbon::parse($p->calving_date)->toDateString() : null,
            ];
        });

        $totalConfirmed = $rows->count();
        $totalLosses = $rows->where('loss', true)->count();
        $lossRate = $totalConfirmed > 0 ? round(($totalLosses / $totalConfirmed) * 100, 2) : 0;

        $lossTypeCounts = [
            'abortion' => $rows->where('loss_type', 'abortion')->count(),
            'embryonic_death' => $rows->where('loss_type', 'embryonic_death')->count(),
            'miscarriage' => $rows->where('loss_type', 'miscarriage')->count(),
            'unknown' => $rows->where('loss', true)->where('loss_type', 'unknown')->count(),
        ];

        $grouped = $this->buildGroupedRows($rows, $groupBy);

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_confirmed_pregnancies' => $totalConfirmed,
            'pregnancy_losses' => $totalLosses,
            'pregnancy_loss_rate' => $lossRate,
            'loss_type_counts' => $lossTypeCounts,
        ];

        return Inertia::render('Reports/PregnancyLossAnalysisReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'group_by' => $groupBy,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $grouped,
            'details' => $rows->values(),
        ]);
    }

    private function lossDate(Pregnancy $p): ?string
    {
        // Prefer explicit loss dates if present.
        if (!empty($p->abortion_date)) {
            return (string) $p->abortion_date;
        }
        if (!empty($p->embryonic_death_date)) {
            return (string) $p->embryonic_death_date;
        }
        if (!empty($p->miscarriage_date)) {
            return (string) $p->miscarriage_date;
        }

        return null;
    }

    private function lossType(Pregnancy $p): string
    {
        if (!empty($p->abortion_date)) {
            return 'abortion';
        }
        if (!empty($p->embryonic_death_date)) {
            return 'embryonic_death';
        }
        if (!empty($p->miscarriage_date)) {
            return 'miscarriage';
        }

        // If status indicates loss but no date fields exist, mark unknown.
        $status = strtolower(trim((string) ($p->pregnancy_status?->value ?? $p->pregnancy_status)));
        if (in_array($status, ['aborted', 'abortion', 'embryonic_death', 'embryonicdeath', 'miscarriage', 'lost', 'pregnancy_loss'], true)) {
            return 'unknown';
        }

        return 'unknown';
    }

    private function lossTimingBucket(?int $daysFromConfirm): ?string
    {
        if ($daysFromConfirm === null) {
            return null;
        }

        // Benchmarks in prompt:
        // Early embryonic loss (before 42 days)
        // Mid-gestation loss
        // Late abortion
        if ($daysFromConfirm < 42) {
            return 'early_embryonic';
        }
        if ($daysFromConfirm < 180) {
            return 'mid_gestation';
        }

        return 'late_abortion';
    }

    private function buildGroupedRows($rows, string $groupBy)
    {
        if ($groupBy === 'month') {
            return $rows
                ->groupBy(function ($r) {
                    // confirmed_date is YYYY-MM-DD
                    return $r['confirmed_date'] ? substr($r['confirmed_date'], 0, 7) : 'Unknown';
                })
                ->map(function ($items, $key) {
                    $confirmed = $items->count();
                    $losses = $items->where('loss', true)->count();
                    $rate = $confirmed > 0 ? round(($losses / $confirmed) * 100, 2) : 0;

                    return [
                        'group' => $key,
                        'confirmed' => $confirmed,
                        'losses' => $losses,
                        'rate' => $rate,
                    ];
                })
                ->values();
        }

        // Default: group by loss timing bucket
        return $rows
            ->where('loss', true)
            ->groupBy('loss_timing')
            ->map(function ($items, $key) {
                $losses = $items->count();

                return [
                    'group' => $this->lossTimingLabel((string) $key),
                    'losses' => $losses,
                ];
            })
            ->values();
    }

    private function lossTimingLabel(string $bucket): string
    {
        return match ($bucket) {
            'early_embryonic' => 'Early embryonic loss (< 42 days)',
            'mid_gestation' => 'Mid-gestation loss (42–179 days)',
            'late_abortion' => 'Late abortion (≥ 180 days)',
            default => 'Unknown',
        };
    }
}
