<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ArtificialInsemination;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AiVsNaturalBreedingSuccessReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('aiVsNaturalBreedingSuccessReport', ArtificialInsemination::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'method' => ['nullable', 'string', 'in:all,ai,natural_mating'],
            'group_by' => ['nullable', 'string', 'in:method,month,technician,bull'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $method = $validated['method'] ?? 'all';
        $groupBy = $validated['group_by'] ?? 'method';

        $animals = Animal::query()
            ->select(['id', 'tag', 'name'])
            ->when($request->user()->hasRole('farm owner'), function ($query) use ($request) {
                $query->where('farm_id', $request->user()->farm_id);
            }) // Only list animals from the user's farm (multi-tenant isolation).
            ->orderBy('tag')
            ->limit(500)
            ->get();

        // Breeding attempts are represented by reproduction_records.
        // We treat each record as 1 attempt, and classify by event.
        $attemptsQuery = ReproductionRecord::query()
            ->select(['id', 'animal_id', 'event', 'event_date', 'performed_by'])
            ->whereBetween('event_date', [$from, $to]);

        if (!empty($validated['animal_id'])) {
            $attemptsQuery->where('animal_id', $validated['animal_id']);
        }

        if ($method !== 'all') {
            $attemptsQuery->whereIn('event', $this->eventsForMethod($method));
        } else {
            $attemptsQuery->whereIn('event', $this->eventsForMethod('all'));
        }

        $attempts = $attemptsQuery
            ->orderByDesc('event_date')
            ->limit(10000)
            ->get();

        $attemptIds = $attempts->pluck('id')->all();

        // Confirmed pregnancies: pregnancies linked to reproduction_record_id
        // and having a confirmed date.
        $confirmedPregnancies = Pregnancy::query()
            ->select(['id', 'reproduction_record_id', 'pregnancy_confirmed_date', 'pregnancy_status'])
            ->whereIn('reproduction_record_id', $attemptIds)
            ->whereNotNull('pregnancy_confirmed_date')
            ->get()
            ->keyBy('reproduction_record_id');

        $rows = $attempts->map(function ($a) use ($confirmedPregnancies) {
            $method = $this->methodFromEvent($a->event);
            $isConfirmed = $confirmedPregnancies->has($a->id);

            return [
                'date' => optional($a->event_date)->toDateString(),
                'animal_id' => $a->animal_id,
                'method' => $method,
                'method_label' => $this->methodLabel($method),
                'event' => $a->event,
                'technician_name' => $a->performed_by,
                // For natural mating, we don't have a direct "bull" field in reproduction_records.
                // If needed, this would require additional modeling (e.g., linking to a bull animal).
                'bull_name' => $method === 'natural_mating' ? 'Unknown Bull' : null,
                'confirmed' => $isConfirmed,
            ];
        });

        $totalAttempts = $rows->count();
        $totalConfirmed = $rows->where('confirmed', true)->count();
        $successRate = $totalAttempts > 0 ? round(($totalConfirmed / $totalAttempts) * 100, 2) : 0;

        $spc = $totalConfirmed > 0 ? round($totalAttempts / $totalConfirmed, 2) : null;

        $firstService = $this->buildFirstServiceSummary($rows);

        $grouped = $this->buildGroupedRows($rows, $groupBy);

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_services' => $totalAttempts,
            'confirmed_pregnancies' => $totalConfirmed,
            'success_rate' => $successRate,
            'services_per_conception' => $spc,
            'first_service' => $firstService,
            'benchmarks' => [
                'ai' => ['min' => 35, 'max' => 55],
                'natural_mating' => ['min' => 50, 'max' => 70],
            ],
        ];

        return Inertia::render('Reports/AiVsNaturalBreedingSuccessReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'method' => $method,
                'group_by' => $groupBy,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $grouped,
        ]);
    }

    public function print(Request $request)
    {
        $this->authorize('aiVsNaturalBreedingSuccessReport', ArtificialInsemination::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'method' => ['nullable', 'string', 'in:all,ai,natural_mating'],
            'group_by' => ['nullable', 'string', 'in:method,month,technician,bull'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $method = $validated['method'] ?? 'all';
        $groupBy = $validated['group_by'] ?? 'method';

        // Breeding attempts are represented by reproduction_records.
        $attemptsQuery = ReproductionRecord::query()
            ->select(['id', 'animal_id', 'event', 'event_date', 'technician_name', 'bull_name'])
            ->whereBetween('event_date', [$from, $to]);

        if (!empty($validated['animal_id'])) {
            $attemptsQuery->where('animal_id', $validated['animal_id']);
        }

        if ($method !== 'all') {
            $attemptsQuery->whereIn('event', $this->eventsForMethod($method));
        } else {
            $attemptsQuery->whereIn('event', $this->eventsForMethod('all'));
        }

        $attempts = $attemptsQuery
            ->orderByDesc('event_date')
            ->limit(10000)
            ->get();

        $attemptIds = $attempts->pluck('id')->all();

        $confirmedPregnancies = Pregnancy::query()
            ->select(['id', 'reproduction_record_id', 'pregnancy_confirmed_date', 'pregnancy_status'])
            ->whereIn('reproduction_record_id', $attemptIds)
            ->whereNotNull('pregnancy_confirmed_date')
            ->get()
            ->keyBy('reproduction_record_id');

        $rows = $attempts->map(function ($a) use ($confirmedPregnancies) {
            $m = $this->methodFromEvent($a->event);
            $isConfirmed = $confirmedPregnancies->has($a->id);

            return [
                'date' => optional($a->event_date)->toDateString(),
                'animal_id' => $a->animal_id,
                'method' => $m,
                'method_label' => $this->methodLabel($m),
                'event' => $a->event,
                'technician_name' => $a->technician_name,
                'bull_name' => $a->bull_name,
                'confirmed' => $isConfirmed,
            ];
        });

        $totalAttempts = $rows->count();
        $totalConfirmed = $rows->where('confirmed', true)->count();
        $successRate = $totalAttempts > 0 ? round(($totalConfirmed / $totalAttempts) * 100, 2) : 0;

        $spc = $totalConfirmed > 0 ? round($totalAttempts / $totalConfirmed, 2) : null;

        $firstService = $this->buildFirstServiceSummary($rows);

        $grouped = $this->buildGroupedRows($rows, $groupBy);

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_services' => $totalAttempts,
            'confirmed_pregnancies' => $totalConfirmed,
            'success_rate' => $successRate,
            'services_per_conception' => $spc,
            'first_service' => $firstService,
            'benchmarks' => [
                'ai' => ['min' => 35, 'max' => 55],
                'natural_mating' => ['min' => 50, 'max' => 70],
            ],
        ];

        $animal = null;
        if (!empty($validated['animal_id'])) {
            $animal = Animal::query()
                ->select(['id', 'tag', 'name'])
                ->find($validated['animal_id']);
        }

        return view('reports.ai-vs-natural-breeding-success.print', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'method' => $method,
                'group_by' => $groupBy,
            ],
            'summary' => $summary,
            'rows' => $grouped,
            'animal' => $animal,
            'generatedAt' => now()->toDateTimeString(),
        ]);
    }

    private function buildFirstServiceSummary($rows): array
    {
        // First-service conception rate:
        // For each animal, take the earliest service in the period and see if it was confirmed.
        $firstByAnimal = $rows
            ->filter(fn($r) => !empty($r['animal_id']) && !empty($r['date']))
            ->sortBy('date')
            ->groupBy('animal_id')
            ->map(fn($items) => $items->first());

        $total = $firstByAnimal->count();
        $confirmed = $firstByAnimal->where('confirmed', true)->count();
        $rate = $total > 0 ? round(($confirmed / $total) * 100, 2) : 0;

        $byMethod = $firstByAnimal
            ->groupBy('method')
            ->map(function ($items, $key) {
                $t = $items->count();
                $c = $items->where('confirmed', true)->count();
                $r = $t > 0 ? round(($c / $t) * 100, 2) : 0;

                return [
                    'method' => $key,
                    'method_label' => $this->methodLabel($key),
                    'total' => $t,
                    'confirmed' => $c,
                    'rate' => $r,
                ];
            })
            ->values()
            ->all();

        return [
            'total_first_services' => $total,
            'confirmed_first_services' => $confirmed,
            'first_service_conception_rate' => $rate,
            'by_method' => $byMethod,
        ];
    }

    private function buildGroupedRows($rows, string $groupBy)
    {
        if ($groupBy === 'month') {
            return $rows
                ->groupBy(function ($r) {
                    return $r['date'] ? substr($r['date'], 0, 7) : 'Unknown';
                })
                ->map(function ($items, $key) {
                    return $this->groupStats($items, $key);
                })
                ->values();
        }

        if ($groupBy === 'technician') {
            return $rows
                ->filter(fn($r) => $r['method'] === 'ai')
                ->groupBy(function ($r) {
                    return trim((string) ($r['technician_name'] ?? '')) ?: 'Unknown';
                })
                ->map(function ($items, $key) {
                    return $this->groupStats($items, $key);
                })
                ->values();
        }

        if ($groupBy === 'bull') {
            return $rows
                ->filter(fn($r) => $r['method'] === 'natural_mating')
                ->groupBy(function ($r) {
                    return trim((string) ($r['bull_name'] ?? '')) ?: 'Unknown';
                })
                ->map(function ($items, $key) {
                    return $this->groupStats($items, $key);
                })
                ->values();
        }

        // Default: group by method
        return $rows
            ->groupBy('method')
            ->map(function ($items, $key) {
                return $this->groupStats($items, $this->methodLabel($key));
            })
            ->values();
    }

    private function groupStats($items, string $groupLabel): array
    {
        $services = $items->count();
        $confirmed = $items->where('confirmed', true)->count();
        $rate = $services > 0 ? round(($confirmed / $services) * 100, 2) : 0;
        $spc = $confirmed > 0 ? round($services / $confirmed, 2) : null;

        return [
            'group' => $groupLabel,
            'services' => $services,
            'confirmed' => $confirmed,
            'success_rate' => $rate,
            'services_per_conception' => $spc,
        ];
    }

    private function eventsForMethod(string $method): array
    {
        // Mappings based on observed DB values (via tinker) + common variants.
        // Current distinct events include: artificial_insemination, mating.
        $ai = ['ai', 'artificial_insemination', 'artificial insemination', 'Artificial Insemination'];
        $natural = ['natural_mating', 'natural mating', 'mating', 'Natural Mating'];

        return match ($method) {
            'ai' => $ai,
            'natural_mating' => $natural,
            default => array_values(array_unique(array_merge($ai, $natural))),
        };
    }

    private function methodFromEvent(?string $event): string
    {
        $e = strtolower(trim((string) $event));

        if (in_array($e, array_map('strtolower', $this->eventsForMethod('ai')), true)) {
            return 'ai';
        }

        if (in_array($e, array_map('strtolower', $this->eventsForMethod('natural_mating')), true)) {
            return 'natural_mating';
        }

        return 'other';
    }

    private function methodLabel(string $method): string
    {
        return match ($method) {
            'ai' => 'Artificial Insemination (AI)',
            'natural_mating' => 'Natural Mating',
            'other' => 'Other / Unmapped',
            default => 'All',
        };
    }
}
