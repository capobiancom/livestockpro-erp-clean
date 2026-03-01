<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\DiseaseTreatment;
use App\Models\HealthEvent;
use App\Models\HealthIssue;
use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class AnimalHealthReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('animalHealthReport', HealthEvent::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'health_issue_id' => ['nullable', 'integer', 'exists:health_issues,id'],
            'event_type' => ['nullable', 'string', 'in:health_events,disease_treatments,vaccinations'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $eventType = $validated['event_type'] ?? 'health_events';

        $animals = Animal::query()
            ->select(['id', 'tag', 'name'])
            ->orderBy('tag')
            ->limit(500)
            ->get();

        $healthIssues = HealthIssue::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $rows = collect();

        if ($eventType === 'health_events') {
            $query = HealthEvent::query()
                ->with([
                    'animal:id,tag,name',
                    'healthIssue:id,name',
                ])
                ->whereBetween('occurred_at', [$from, $to])
                ->orderByDesc('occurred_at');

            if (!empty($validated['animal_id'])) {
                $query->where('animal_id', $validated['animal_id']);
            }

            if (!empty($validated['health_issue_id'])) {
                $query->where('health_issue_id', $validated['health_issue_id']);
            }

            $rows = $query->limit(2000)->get()->map(function ($e) {
                return [
                    'date' => optional($e->occurred_at)->toDateString(),
                    'animal' => trim(($e->animal?->tag ?? '') . ' ' . ($e->animal?->name ?? '')),
                    'category' => 'Health Event',
                    'issue' => $e->healthIssue?->name,
                    'notes' => $e->notes ?? null,
                    'status' => $e->status ?? null,
                ];
            });
        } elseif ($eventType === 'disease_treatments') {
            $query = DiseaseTreatment::query()
                ->with([
                    'animal:id,tag,name',
                    'disease:id,name',
                    'treatment:id,name',
                ])
                ->whereBetween('started_at', [$from, $to])
                ->orderByDesc('started_at');

            if (!empty($validated['animal_id'])) {
                $query->where('animal_id', $validated['animal_id']);
            }

            $rows = $query->limit(2000)->get()->map(function ($t) {
                return [
                    'date' => optional($t->started_at)->toDateString(),
                    'animal' => trim(($t->animal?->tag ?? '') . ' ' . ($t->animal?->name ?? '')),
                    'category' => 'Disease Treatment',
                    'issue' => $t->disease?->name,
                    'notes' => $t->treatment?->name,
                    'status' => $t->status ?? null,
                ];
            });
        } else {
            $query = VaccinationRecord::query()
                ->with([
                    'animal:id,tag,name',
                    'vaccineType:id,name',
                ])
                ->whereBetween('vaccination_date', [$from, $to])
                ->orderByDesc('vaccination_date');

            if (!empty($validated['animal_id'])) {
                $query->where('animal_id', $validated['animal_id']);
            }

            $rows = $query->limit(2000)->get()->map(function ($v) {
                return [
                    'date' => optional($v->vaccination_date)->toDateString(),
                    'animal' => trim(($v->animal?->tag ?? '') . ' ' . ($v->animal?->name ?? '')),
                    'category' => 'Vaccination',
                    'issue' => $v->vaccineType?->name,
                    'notes' => $v->notes ?? null,
                    'status' => $v->status ?? null,
                ];
            });
        }

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total' => $rows->count(),
        ];

        return Inertia::render('Reports/AnimalHealthReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'health_issue_id' => $validated['health_issue_id'] ?? null,
                'event_type' => $eventType,
            ],
            'animals' => $animals,
            'healthIssues' => $healthIssues,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
