<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\DiseaseTreatment;
use App\Models\DiseaseTreatmentMedication;
use App\Models\HealthEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TreatmentCostPerAnimalReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('treatmentCostPerCowReport', DiseaseTreatment::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:total_cost,animal'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfMonth()->endOfDay();

        $sort = $validated['sort'] ?? 'total_cost';
        $direction = $validated['direction'] ?? 'desc';

        $animals = Animal::query()
            ->select(['id', 'tag_number', 'name'])
            ->orderBy('tag_number')
            ->limit(500)
            ->get();

        // 1) Health events costs (vet_fee, lab_cost, other_cost, cost)
        $healthEventCosts = HealthEvent::query()
            ->select([
                'animal_id',
                DB::raw('COALESCE(SUM(cost),0) as medicine_cost'),
                DB::raw('COALESCE(SUM(vet_fee),0) as vet_fee'),
                DB::raw('COALESCE(SUM(lab_cost),0) as lab_cost'),
                DB::raw('COALESCE(SUM(other_cost),0) as other_cost'),
            ])
            ->whereBetween('occurred_at', [$from, $to])
            ->groupBy('animal_id');

        if (!empty($validated['animal_id'])) {
            $healthEventCosts->where('animal_id', $validated['animal_id']);
        }

        // 2) Disease treatment medication costs (sum total_cost)
        // We join through disease_treatments -> health_events to get animal_id and date range.
        $medicationCosts = DiseaseTreatmentMedication::query()
            ->join('disease_treatments', 'disease_treatments.id', '=', 'disease_treatment_medications.disease_treatment_id')
            ->join('health_events', 'health_events.id', '=', 'disease_treatments.health_event_id')
            ->select([
                'health_events.animal_id as animal_id',
                DB::raw('COALESCE(SUM(disease_treatment_medications.total_cost),0) as medication_cost'),
            ])
            ->whereBetween('health_events.occurred_at', [$from, $to])
            ->groupBy('health_events.animal_id');

        if (!empty($validated['animal_id'])) {
            $medicationCosts->where('health_events.animal_id', $validated['animal_id']);
        }

        // Merge both sources in PHP (small result set: per animal)
        $healthByAnimal = $healthEventCosts->get()->keyBy('animal_id');
        $medByAnimal = $medicationCosts->get()->keyBy('animal_id');

        $rows = $animals
            ->map(function ($a) use ($healthByAnimal, $medByAnimal) {
                $h = $healthByAnimal->get($a->id);
                $m = $medByAnimal->get($a->id);

                $medicineCost = (float) ($h->medicine_cost ?? 0);
                $vetFee = (float) ($h->vet_fee ?? 0);
                $labCost = (float) ($h->lab_cost ?? 0);
                $otherCost = (float) ($h->other_cost ?? 0);
                $medicationCost = (float) ($m->medication_cost ?? 0);

                // Avoid double counting: if HealthEvent.cost already represents medicine cost,
                // we keep it as "medicine_cost" and show medication_cost separately.
                $total = $medicationCost + $vetFee + $labCost + $otherCost;

                return [
                    'animal_id' => $a->id,
                    'animal_tag' => $a->tag_number,
                    'animal_name' => $a->name,
                    'medicine_cost' => $medicineCost,
                    'treatment_medication_cost' => $medicationCost,
                    'vet_fee' => $vetFee,
                    'lab_cost' => $labCost,
                    'other_cost' => $otherCost,
                    'total_cost' => $total,
                ];
            })
            ->filter(function ($r) use ($validated) {
                if (!empty($validated['animal_id']) && (int) $validated['animal_id'] !== (int) $r['animal_id']) {
                    return false;
                }

                if (!empty($validated['q'])) {
                    $q = mb_strtolower(trim($validated['q']));
                    $hay = mb_strtolower(trim(($r['animal_tag'] ?? '') . ' ' . ($r['animal_name'] ?? '')));
                    return str_contains($hay, $q);
                }

                return true;
            })
            ->values();

        if ($sort === 'animal') {
            $rows = $rows->sortBy(function ($r) {
                return trim(($r['animal_tag'] ?? '') . ' ' . ($r['animal_name'] ?? ''));
            }, SORT_NATURAL | SORT_FLAG_CASE, $direction === 'desc')->values();
        } else {
            $rows = $rows->sortBy('total_cost', SORT_REGULAR, $direction === 'desc')->values();
        }

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'animals' => $rows->count(),
            'total_cost' => (float) $rows->sum('total_cost'),
            'total_medicine_cost' => (float) $rows->sum('medicine_cost'),
            'total_treatment_medication_cost' => (float) $rows->sum('treatment_medication_cost'),
            'total_vet_fee' => (float) $rows->sum('vet_fee'),
            'total_lab_cost' => (float) $rows->sum('lab_cost'),
            'total_other_cost' => (float) $rows->sum('other_cost'),
        ];

        return Inertia::render('Reports/TreatmentCostPerAnimalReports/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'animal_id' => $validated['animal_id'] ?? null,
                'q' => $validated['q'] ?? null,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'animals' => $animals,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
