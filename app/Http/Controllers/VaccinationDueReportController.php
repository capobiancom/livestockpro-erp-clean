<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\VaccinationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class VaccinationDueReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('vaccinationDueReport', VaccinationRecord::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'status' => ['nullable', 'string', 'in:all,due,overdue,upcoming'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->addDays(30)->endOfDay();

        $status = $validated['status'] ?? 'all';

        $animals = Animal::query()
            ->select(['id', 'tag', 'name'])
            ->orderBy('tag')
            ->limit(1000)
            ->get()
            ->map(function ($a) {
                $tag = $a->tag_number ?? $a->tag ?? '';
                return [
                    'id' => $a->id,
                    'label' => trim($tag . ' ' . ($a->name ?? '')),
                ];
            });

        $query = VaccinationRecord::query()
            ->with([
                'animal:id,tag,name',
                'disease:id,name',
                'staff:id,first_name,last_name,position',
            ])
            ->whereNotNull('next_due_at')
            ->whereBetween('next_due_at', [$from, $to])
            ->orderBy('next_due_at', 'asc');

        if (!empty($validated['animal_id'])) {
            $query->where('animal_id', $validated['animal_id']);
        }

        $now = now();

        if ($status === 'overdue') {
            $query->where('next_due_at', '<', $now);
        } elseif ($status === 'due') {
            $query->whereBetween('next_due_at', [$now->copy()->startOfDay(), $now->copy()->endOfDay()]);
        } elseif ($status === 'upcoming') {
            $query->where('next_due_at', '>', $now);
        }

        $rows = $query->limit(5000)->get()->map(function ($v) use ($now) {
            $dueAt = $v->next_due_at;
            $daysLeft = $dueAt ? $now->startOfDay()->diffInDays($dueAt->copy()->startOfDay(), false) : null;

            $animalTag = $v->animal?->tag_number ?? $v->animal?->tag ?? '';
            $animalLabel = trim($animalTag . ' ' . ($v->animal?->name ?? ''));

            $staffName = trim(($v->staff?->first_name ?? '') . ' ' . ($v->staff?->last_name ?? ''));

            $state = 'Upcoming';
            if ($daysLeft !== null && $daysLeft < 0) {
                $state = 'Overdue';
            } elseif ($daysLeft === 0) {
                $state = 'Due Today';
            }

            return [
                'due_date' => optional($dueAt)->toDateString(),
                'animal' => $animalLabel ?: '—',
                'disease' => $v->disease?->name ?? '—',
                'staff' => $staffName ?: '—',
                'days_left' => $daysLeft,
                'status' => $state,
                'notes' => $v->notes ?? null,
            ];
        })->values();

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total' => $rows->count(),
            'overdue' => $rows->where('status', 'Overdue')->count(),
            'due_today' => $rows->where('status', 'Due Today')->count(),
            'upcoming' => $rows->where('status', 'Upcoming')->count(),
        ];

        return Inertia::render('Reports/VaccinationDueReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'status' => $status,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
