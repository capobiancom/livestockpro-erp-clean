<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Pregnancy;
use App\Models\ReproductionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class ConceptionSuccessRateReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('conseptionSuccessRateReport', ReproductionRecord::class);

        // NOTE: No dedicated policy exists yet for this report.
        // We keep it behind auth middleware via routes/web.php.

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'service_type' => ['nullable', 'string', 'in:all,ai,natural_mating,embryo_transfer'],
            'group_by' => ['nullable', 'string', 'in:service_type,month'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $serviceType = $validated['service_type'] ?? 'all';
        $groupBy = $validated['group_by'] ?? 'service_type';

        $animals = Animal::query()
            ->select(['id', 'tag_number', 'name'])
            ->orderBy('tag_number')
            ->limit(500)
            ->get();

        // Breeding attempts are represented by reproduction_records.
        // We treat each record as 1 attempt, and classify by event.
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
            ->orderByDesc('event_date')
            ->limit(5000)
            ->get();

        $attemptIds = $attempts->pluck('id')->all();

        // Confirmed pregnancies: pregnancies linked to reproduction_record_id
        // and having a confirmed date (or status if used).
        $confirmedPregnancies = Pregnancy::query()
            ->select(['id', 'reproduction_record_id', 'pregnancy_confirmed_date', 'pregnancy_status'])
            ->whereIn('reproduction_record_id', $attemptIds)
            ->whereNotNull('pregnancy_confirmed_date')
            ->get()
            ->keyBy('reproduction_record_id');

        $rows = $attempts->map(function ($a) use ($confirmedPregnancies) {
            $serviceType = $this->serviceTypeFromEvent($a->event);
            $isConfirmed = $confirmedPregnancies->has($a->id);

            return [
                'date' => optional($a->event_date)->toDateString(),
                'animal_id' => $a->animal_id,
                'service_type' => $serviceType,
                'service_type_label' => $this->serviceTypeLabel($serviceType),
                'event' => $a->event,
                'confirmed' => $isConfirmed,
            ];
        });

        $totalAttempts = $rows->count();
        $totalConfirmed = $rows->where('confirmed', true)->count();
        $rate = $totalAttempts > 0 ? round(($totalConfirmed / $totalAttempts) * 100, 2) : 0;

        $grouped = $this->buildGroupedRows($rows, $groupBy);

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_attempts' => $totalAttempts,
            'confirmed_pregnancies' => $totalConfirmed,
            'conception_success_rate' => $rate,
        ];

        return Inertia::render('Reports/ConceptionSuccessRateReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'service_type' => $serviceType,
                'group_by' => $groupBy,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $grouped,
        ]);
    }

    private function buildGroupedRows($rows, string $groupBy)
    {
        if ($groupBy === 'month') {
            return $rows
                ->groupBy(function ($r) {
                    // r['date'] is YYYY-MM-DD
                    return $r['date'] ? substr($r['date'], 0, 7) : 'Unknown';
                })
                ->map(function ($items, $key) {
                    $attempts = $items->count();
                    $confirmed = $items->where('confirmed', true)->count();
                    $rate = $attempts > 0 ? round(($confirmed / $attempts) * 100, 2) : 0;

                    return [
                        'group' => $key,
                        'attempts' => $attempts,
                        'confirmed' => $confirmed,
                        'rate' => $rate,
                    ];
                })
                ->values();
        }

        // Default: group by service type
        return $rows
            ->groupBy('service_type')
            ->map(function ($items, $key) {
                $attempts = $items->count();
                $confirmed = $items->where('confirmed', true)->count();
                $rate = $attempts > 0 ? round(($confirmed / $attempts) * 100, 2) : 0;

                return [
                    'group' => $this->serviceTypeLabel($key),
                    'attempts' => $attempts,
                    'confirmed' => $confirmed,
                    'rate' => $rate,
                ];
            })
            ->values();
    }

    private function eventsForServiceType(string $serviceType): array
    {
        // These are best-effort mappings based on ReproductionRecord.event.
        // If your DB uses different strings, we can adjust quickly.
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

    private function serviceTypeFromEvent(?string $event): string
    {
        $e = strtolower(trim((string) $event));

        if (in_array($e, array_map('strtolower', $this->eventsForServiceType('ai')), true)) {
            return 'ai';
        }

        if (in_array($e, array_map('strtolower', $this->eventsForServiceType('natural_mating')), true)) {
            return 'natural_mating';
        }

        if (in_array($e, array_map('strtolower', $this->eventsForServiceType('embryo_transfer')), true)) {
            return 'embryo_transfer';
        }

        return 'other';
    }

    private function serviceTypeLabel(string $serviceType): string
    {
        return match ($serviceType) {
            'ai' => 'Artificial Insemination (AI)',
            'natural_mating' => 'Natural Mating',
            'embryo_transfer' => 'Embryo Transfer',
            'other' => 'Other / Unmapped',
            default => 'All',
        };
    }
}
