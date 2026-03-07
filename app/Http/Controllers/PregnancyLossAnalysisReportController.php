<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Pregnancy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class PregnancyLossAnalysisReportController extends Controller
{
    public function print(Request $request)
    {
        $this->authorize('pregnancyLossAnalysis', Pregnancy::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer'],
            'group_by' => ['nullable', 'string', 'in:loss_timing,month'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $groupBy = $validated['group_by'] ?? 'loss_timing';

        $activeFarmId = session('farm_id') ?: $request->user()?->farm_id;

        if (!empty($validated['animal_id'])) {
            abort_unless(
                Animal::query()
                    ->when($activeFarmId, fn($q) => $q->where('farm_id', $activeFarmId))
                    ->whereKey($validated['animal_id'])
                    ->exists(),
                422,
                'Invalid animal selection.'
            );
        }

        $select = [
            'id',
            'animal_id',
            'pregnancy_confirmed_date',
            'pregnancy_status',
            'expected_calving_date',
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'updated_at')) {
            $select[] = 'updated_at';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'embryonic_death_date')) {
            $select[] = 'embryonic_death_date';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'miscarriage_date')) {
            $select[] = 'miscarriage_date';
        }

        $confirmedQuery = Pregnancy::query()
            ->select($select)
            ->with([
                'animal:id,tag,name',
            ])
            ->whereNotNull('pregnancy_confirmed_date')
            ->whereBetween('pregnancy_confirmed_date', [$from, $to])
            ->whereHas('animal', function ($q) use ($activeFarmId) {
                if ($activeFarmId) {
                    $q->where('farm_id', $activeFarmId);
                }
            });

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
                $daysFromConfirm = round(
                    Carbon::parse($p->pregnancy_confirmed_date)->diffInDays(Carbon::parse($lossDate), false),
                    2
                );
                $timing = $this->lossTimingBucket($daysFromConfirm);
            }

            return [
                'pregnancy_id' => $p->id,
                'animal_id' => $p->animal_id,
                'animal_tag' => $p->animal?->tag,
                'animal_name' => $p->animal?->name,
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
            'abortion' => $rows->where('loss_type', 'aborted')->count(),
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

        return view('reports.pregnancy-loss-analysis.print', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'group_by' => $groupBy,
            ],
            'summary' => $summary,
            'rows' => $grouped,
            'details' => $rows->values(),
            'generated_at' => now()->toDateTimeString(),
            'farm' => $request->user()?->farm,
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize('pregnancyLossAnalysis', Pregnancy::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer'],
            'group_by' => ['nullable', 'string', 'in:loss_timing,month'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $groupBy = $validated['group_by'] ?? 'loss_timing';

        // Animals dropdown should only show animals for the currently active farm.
        // FarmScope should apply to Animal; additionally guard by farm_id if available.
        $animalsQuery = Animal::query()
            ->select(['id', 'tag', 'name'])
            ->when($request->user()->hasRole('farm owner'), function ($query) use ($request) {
                $query->where('farm_id', $request->user()->farm_id);
            }) // Only list animals from the user's farm (multi-tenant isolation).
            ->orderBy('tag')
            ->limit(500);

        $activeFarmId = session('farm_id') ?: $request->user()?->farm_id;

        if ($activeFarmId) {
            $animalsQuery->where('farm_id', $activeFarmId);
        }

        $animals = $animalsQuery->get();

        // Ensure selected animal belongs to the currently active farm (FarmScope applies).
        if (!empty($validated['animal_id'])) {
            abort_unless(
                Animal::query()
                    ->when($activeFarmId, fn($q) => $q->where('farm_id', $activeFarmId))
                    ->whereKey($validated['animal_id'])
                    ->exists(),
                422,
                'Invalid animal selection.'
            );
        }

        // Confirmed pregnancies within range (by confirmed date).
        // older installations may not have individual loss-date columns, so
        // include them only when present to avoid SQL errors.
        $select = [
            'id',
            'animal_id',
            'pregnancy_confirmed_date',
            'pregnancy_status',
            'expected_calving_date',
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'updated_at')) {
            $select[] = 'updated_at';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'embryonic_death_date')) {
            $select[] = 'embryonic_death_date';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'miscarriage_date')) {
            $select[] = 'miscarriage_date';
        }

        $confirmedQuery = Pregnancy::query()
            ->select($select)
            ->with([
                'animal:id,tag,name',
            ])
            ->whereNotNull('pregnancy_confirmed_date')
            ->whereBetween('pregnancy_confirmed_date', [$from, $to])
            // Enforce farm scoping through the related animal (works even if Pregnancy has no FarmScope).
            ->whereHas('animal', function ($q) use ($activeFarmId) {
                if ($activeFarmId) {
                    $q->where('farm_id', $activeFarmId);
                }
            });

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
                $daysFromConfirm = round(
                    Carbon::parse($p->pregnancy_confirmed_date)->diffInDays(Carbon::parse($lossDate), false),
                    2
                );
                $timing = $this->lossTimingBucket($daysFromConfirm);
            }

            return [
                'pregnancy_id' => $p->id,
                'animal_id' => $p->animal_id,
                'animal_tag' => $p->animal?->tag,
                'animal_name' => $p->animal?->name,
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
            'abortion' => $rows->where('loss_type', 'aborted')->count(),
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
        // Prefer explicit loss dates if present.  Guard against missing
        // columns by checking schema first; accessing non-existent attributes
        // will simply return null, but being explicit makes intent clear.
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'updated_at')
            && !empty($p->updated_at) && $p->pregnancy_status && str_contains(strtolower($p->pregnancy_status?->value ?? (string) $p->pregnancy_status), 'aborted')
        ) {
            return (string) $p->updated_at;
        }
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'embryonic_death_date')
            && !empty($p->embryonic_death_date)
        ) {
            return (string) $p->embryonic_death_date;
        }
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'miscarriage_date')
            && !empty($p->miscarriage_date)
        ) {
            return (string) $p->miscarriage_date;
        }

        return null;
    }

    private function lossType(Pregnancy $p): string
    {
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'updated_at')
            && !empty($p->updated_at) && $p->pregnancy_status && str_contains(strtolower($p->pregnancy_status?->value ?? (string) $p->pregnancy_status), 'aborted')
        ) {
            return 'aborted';
        }
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'embryonic_death_date')
            && !empty($p->embryonic_death_date)
        ) {
            return 'embryonic_death';
        }
        if (
            \Illuminate\Support\Facades\Schema::hasColumn('pregnancies', 'miscarriage_date')
            && !empty($p->miscarriage_date)
        ) {
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
