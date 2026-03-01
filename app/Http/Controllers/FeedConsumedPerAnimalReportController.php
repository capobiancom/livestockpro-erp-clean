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

class FeedConsumedPerAnimalReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('feedConsumedPerAnimalReport', StockMovement::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization: reuse existing "view stock movements" permission if present,
        // otherwise allow farm owners (consistent with other inventory reports).
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'animal_id' => ['nullable', 'integer', 'exists:animals,id'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:animal,tag,total_feed,avg_daily_feed'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $animalId = $validated['animal_id'] ?? null;
        $q = $validated['q'] ?? null;
        $sort = $validated['sort'] ?? 'animal';
        $direction = $validated['direction'] ?? 'asc';

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
            ->selectRaw('COUNT(DISTINCT stock_movements.movement_date) as feeding_days')
            ->groupBy('animals.id', 'animals.tag', 'animals.name');

        // avg_daily_feed = total_feed / feeding_days (days with at least one feeding movement)
        $base->addSelect([
            DB::raw("CASE WHEN COUNT(DISTINCT stock_movements.movement_date) > 0
                THEN (SUM(stock_movements.quantity) / COUNT(DISTINCT stock_movements.movement_date))
                ELSE 0 END as avg_daily_feed"),
        ]);

        $sortMap = [
            'animal' => 'animal_name',
            'tag' => 'animal_tag',
            'total_feed' => 'total_feed',
            'avg_daily_feed' => 'avg_daily_feed',
        ];

        $base->orderBy($sortMap[$sort] ?? 'animal_name', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'animal_id' => (int)$r->animal_id,
                'tag' => $r->animal_tag,
                'name' => $r->animal_name,
                'total_feed' => round((float)$r->total_feed, 2),
                'feeding_days' => (int)$r->feeding_days,
                'avg_daily_feed' => round((float)$r->avg_daily_feed, 2),
            ];
        });

        $summary = [
            'total_animals' => $rows->count(),
            'total_feed' => round($rows->sum('total_feed'), 2),
            'avg_daily_feed_overall' => $rows->count() > 0 ? round($rows->avg('avg_daily_feed'), 2) : 0,
        ];

        // For filter dropdown
        $animals = Animal::query()
            ->select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), fn($qb) => $qb->where('farm_id', $user->farm_id))
            ->orderBy('tag')
            ->limit(5000)
            ->get()
            ->map(fn($a) => [
                'id' => (int)$a->id,
                'tag' => $a->tag,
                'name' => $a->name,
            ]);

        return Inertia::render('Reports/InventoryReports/FeedConsumedPerAnimal/Index', [
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
