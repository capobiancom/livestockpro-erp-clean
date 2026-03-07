<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\DiseaseTreatment;
use App\Models\DiseaseTreatmentMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MedicineUsedPerDiseaseReportController extends Controller
{
    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'disease_id' => ['nullable', 'integer', 'exists:diseases,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:disease,medicine,quantity,cost'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $from = $validated['from'] ?? now()->startOfMonth()->toDateString();
        $to = $validated['to'] ?? now()->endOfMonth()->toDateString();
        $diseaseId = $validated['disease_id'] ?? null;
        $q = $validated['q'] ?? null;
        $sort = $validated['sort'] ?? 'cost';
        $direction = $validated['direction'] ?? 'desc';

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        // Base query: medication usage lines joined to disease treatments and diseases.
        $base = DiseaseTreatmentMedication::query()
            ->join('disease_treatments', 'disease_treatments.id', '=', 'disease_treatment_medications.disease_treatment_id')
            ->join('health_issues', 'health_issues.id', '=', 'disease_treatments.health_issue_id')
            ->join('diseases', 'diseases.id', '=', 'health_issues.disease_id')
            ->leftJoin('medicines', 'medicines.id', '=', 'disease_treatment_medications.medicine_id')
            ->select([
                'diseases.id as disease_id',
                'diseases.name as disease_name',
                'disease_treatment_medications.medicine_id as medicine_id',
                DB::raw('COALESCE(medicines.name, "Unknown") as medicine_name'),
                DB::raw('COALESCE(medicines.unit, "") as unit'),
            ])
            ->selectRaw('SUM(COALESCE(disease_treatment_medications.qty, 0)) as total_quantity')
            ->selectRaw('SUM(COALESCE(disease_treatment_medications.qty, 0) * COALESCE(medicines.unit_cost, 0)) as total_cost')
            ->whereDate('disease_treatments.started_at', '<=', $to)
            ->where(function ($qb) use ($from) {
                $qb->whereNull('disease_treatments.ended_at')
                    ->orWhereDate('disease_treatments.ended_at', '>=', $from);
            })
            ->groupBy('diseases.id', 'diseases.name', 'disease_treatment_medications.medicine_id', 'medicine_name', 'unit');

        if ($user->hasRole('farm_owner')) {
            $base->where('disease_treatments.farm_id', $user->farm_id);
        }

        if (!empty($diseaseId)) {
            $base->where('diseases.id', $diseaseId);
        }

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('diseases.name', 'like', "%{$q}%")
                    ->orWhere('medicines.name', 'like', "%{$q}%");
            });
        }

        $sortMap = [
            'disease' => 'disease_name',
            'medicine' => 'medicine_name',
            'quantity' => 'total_quantity',
            'cost' => 'total_cost',
        ];

        $base->orderBy($sortMap[$sort] ?? 'total_cost', $direction);

        $rows = $base->limit(10000)->get()->map(function ($r) {
            return [
                'disease_id' => (int)$r->disease_id,
                'disease_name' => $r->disease_name,
                'medicine_id' => $r->medicine_id ? (int)$r->medicine_id : null,
                'medicine_name' => $r->medicine_name,
                'unit' => $r->unit,
                'total_quantity' => round((float)$r->total_quantity, 2),
                'total_cost' => round((float)$r->total_cost, 2),
            ];
        });

        $summary = [
            'total_diseases' => $rows->pluck('disease_id')->unique()->count(),
            'total_medicines' => $rows->pluck('medicine_id')->filter()->unique()->count(),
            'total_quantity' => round($rows->sum('total_quantity'), 2),
            'total_cost' => round($rows->sum('total_cost'), 2),
            'note' => $rows->count() === 0 ? 'No medication usage records found. Add Disease Treatment Medications to see quantities/costs.' : null,
        ];

        $diseases = Disease::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($d) => ['id' => (int)$d->id, 'name' => $d->name]);

        $farm = null;
        if (!empty($user->farm_id)) {
            $farm = \App\Models\Farm::query()->find($user->farm_id);
        }

        return [
            'filters' => [
                'from' => $from,
                'to' => $to,
                'disease_id' => $diseaseId,
                'q' => $q,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
            'diseases' => $diseases,
            'farm' => $farm,
        ];
    }

    public function index(Request $request)
    {
        $this->authorize('medicineUseedPerDiseaseReport', DiseaseTreatment::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization aligned with inventory report patterns.
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $report = $this->buildReport($request);

        return Inertia::render('Reports/InventoryReports/MedicineUsedPerDisease/Index', [
            'filters' => $report['filters'],
            'summary' => $report['summary'],
            'rows' => $report['rows'],
            'diseases' => $report['diseases'],
        ]);
    }

    public function print(Request $request)
    {
        $this->authorize('medicineUseedPerDiseaseReport', DiseaseTreatment::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $report = $this->buildReport($request);

        return view('reports.inventory.medicine-used-per-disease.print', [
            'filters' => $report['filters'],
            'summary' => $report['summary'],
            'rows' => $report['rows'],
            'generatedAt' => now(),
            'user' => $user,
            'farm' => $report['farm'],
        ]);
    }
}
