<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\FeedingRecord;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CostOfFeedPerCowReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('costOfFeedPerCowReport', StockMovement::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization: align with other inventory reports
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:animal,tag,total_cost,total_feed,avg_cost_per_kg'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $animalId = $validated['animal_id'] ?? null;
        $q = $validated['q'] ?? null;
        $sort = $validated['sort'] ?? 'total_cost';
        $direction = $validated['direction'] ?? 'desc';

        // Base query: stock movements that represent feed consumption
        // FeedingRecordController creates:
        // - movement_type = 'out'
        // - source_event_type = 'consumption'
        // - source_type = FeedingRecord::class
        // - item_type = InventoryItem::class
        $base = StockMovement::query()
            ->where('stock_movements.movement_type', 'out')
            ->where('stock_movements.source_event_type', 'consumption')
            ->where('stock_movements.source_type', FeedingRecord::class)
            ->where('stock_movements.item_type', InventoryItem::class);

        if ($user->hasRole('farm owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        if ($from) {
            $base->whereDate('stock_movements.movement_date', '>=', $from);
        }
        if ($to) {
            $base->whereDate('stock_movements.movement_date', '<=', $to);
        }

        // Join feeding_records to get animal_id
        $base->join('feeding_records', function ($join) {
            $join->on('feeding_records.id', '=', 'stock_movements.source_id')
                ->where('stock_movements.source_type', '=', FeedingRecord::class);
        });

        // Join animals for display + filtering
        $base->join('animals', 'animals.id', '=', 'feeding_records.animal_id');

        if ($user->hasRole('farm owner')) {
            $base->where('animals.farm_id', $user->farm_id);
        }

        if ($animalId) {
            $base->where('animals.id', $animalId);
        }

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('animals.tag', 'like', "%{$q}%")
                    ->orWhere('animals.name', 'like', "%{$q}%");
            });
        }

        // Aggregate per animal
        $base->select([
            'animals.id as animal_id',
            'animals.tag as animal_tag',
            'animals.name as animal_name',
        ])
            ->selectRaw('SUM(stock_movements.quantity) as total_feed')
            ->selectRaw('SUM(COALESCE(stock_movements.quantity, 0) * COALESCE(stock_movements.unit_cost, 0)) as total_cost')
            ->groupBy('animals.id', 'animals.tag', 'animals.name');

        // avg_cost_per_kg = total_cost / total_feed
        $base->addSelect([
            DB::raw("CASE WHEN SUM(stock_movements.quantity) > 0
                THEN (SUM(COALESCE(stock_movements.quantity, 0) * COALESCE(stock_movements.unit_cost, 0)) / SUM(stock_movements.quantity))
                ELSE 0 END as avg_cost_per_kg"),
        ]);

        $sortMap = [
            'animal' => 'animal_name',
            'tag' => 'animal_tag',
            'total_cost' => 'total_cost',
            'total_feed' => 'total_feed',
            'avg_cost_per_kg' => 'avg_cost_per_kg',
        ];

        $base->orderBy($sortMap[$sort] ?? 'total_cost', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'animal_id' => (int) $r->animal_id,
                'tag' => $r->animal_tag,
                'name' => $r->animal_name,
                'total_feed' => round((float) $r->total_feed, 2),
                'total_cost' => round((float) $r->total_cost, 2),
                'avg_cost_per_kg' => round((float) $r->avg_cost_per_kg, 2),
            ];
        });

        $summary = [
            'total_animals' => $rows->count(),
            'total_feed' => round($rows->sum('total_feed'), 2),
            'total_cost' => round($rows->sum('total_cost'), 2),
            'avg_cost_per_kg_overall' => $rows->sum('total_feed') > 0
                ? round($rows->sum('total_cost') / $rows->sum('total_feed'), 2)
                : 0,
        ];

        // For filter dropdown
        $animals = Animal::query()
            ->select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), fn($qb) => $qb->where('farm_id', $user->farm_id))
            ->orderBy('tag')
            ->limit(5000)
            ->get()
            ->map(fn($a) => [
                'id' => (int) $a->id,
                'tag' => $a->tag,
                'name' => $a->name,
            ]);

        return Inertia::render('Reports/InventoryReports/CostOfFeedPerCow/Index', [
            'filters' => [
                'from' => $from,
                'to' => $to,
                'animal_id' => $animalId,
                'q' => $q,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
            'animals' => $animals,
        ]);
    }
}
