<?php

namespace App\Http\Controllers;

use App\Models\FeedingRecord;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class FeedingCostAnalysisReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('feedingCostAnalysis', FeedingRecord::class);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'inventory_item_id' => ['nullable', 'integer', 'exists:inventory_items,id'],
            'group_by' => ['nullable', 'string', 'in:day,week,month,item'],
        ]);

        $from = isset($validated['from'])
            ? Carbon::parse($validated['from'])->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = isset($validated['to'])
            ? Carbon::parse($validated['to'])->endOfDay()
            : now()->endOfDay();

        $groupBy = $validated['group_by'] ?? 'day';

        // Some installations (older remote databases) may not have the `name`
        // column on inventory_items.  Fall back to sku if necessary and avoid
        // ordering/selecting a non‑existent field so the report doesn't blow up.
        $itemsQuery = InventoryItem::query();
        $columns = ['id', 'unit'];

        if (\Illuminate\Support\Facades\Schema::hasColumn('inventory_items', 'name')) {
            $columns[] = 'name';
            $itemsQuery->orderBy('name');
        } else {
            // SKU is the next best identifier when `name` is missing
            $columns[] = 'sku';
            $itemsQuery->orderBy('sku');
        }

        $items = $itemsQuery->select($columns)
            ->limit(1000)
            ->get();

        // prepare eager loads with conditional animal columns to avoid
        // selecting a non-existent `name` field on legacy databases
        $animalCols = ['id', 'tag_number'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('animals', 'name')) {
            $animalCols[] = 'name';
        }

        // Build dynamic eager loads for feeding items so we don't request
        // columns that may not exist on legacy installations.  In older
        // versions the table stored name/unit/unit_cost fields directly; later
        // a normalized `item_id` foreign key was added instead.
        $feedingItemCols = ['id', 'feeding_record_id', 'quantity'];

        if (\Illuminate\Support\Facades\Schema::hasColumn('feeding_items', 'name')) {
            $feedingItemCols[] = 'name';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('feeding_items', 'unit')) {
            $feedingItemCols[] = 'unit';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('feeding_items', 'unit_cost')) {
            $feedingItemCols[] = 'unit_cost';
        }
        $needItemRelation = false;
        if (\Illuminate\Support\Facades\Schema::hasColumn('feeding_items', 'item_id')) {
            $feedingItemCols[] = 'item_id';
            $needItemRelation = true;
        }

        // build relation strings instead of associative array to avoid
        // Laravel treating the value as a callback
        $relations = [];
        $relations[] = 'feedingItems:' . implode(',', $feedingItemCols);
        if ($needItemRelation) {
            $relations[] = 'feedingItems.item:id,sku,name,unit';
        }
        $relations[] = 'animal:' . implode(',', $animalCols);

        $query = FeedingRecord::query()
            ->with($relations)
            ->whereBetween('feeding_date', [$from, $to])
            ->orderByDesc('feeding_date');

        if (!empty($validated['inventory_item_id'])) {
            // FeedingRecord has a hasMany relation `feedingItems()`.
            // Filter by item id on the related feeding_items table.
            $query->whereHas('feedingItems', function ($q) use ($validated) {
                $q->where('id', $validated['inventory_item_id']);
            });
        }

        $rows = $query->limit(2000)->get()->flatMap(function ($record) {
            return $record->feedingItems->map(function ($item) use ($record) {
                $qty = (float) ($item->quantity ?? 0);
                $unitCost = (float) ($item->unit_cost ?? 0);
                $total = $qty * $unitCost;

                return [
                    'date' => optional($record->feeding_date)->toDateString(),
                    // combine tag and name if available; older schema may lack `name`
                    'animal' => trim(($record->animal?->tag_number ?? '') . ' ' . ($record->animal?->name ?? '')),
                    // item name may come from the feeding_items row (old schema) or
                    // from the joined inventory item (new schema).  use sku if both
                    // are missing.
                    'item' => $item->name
                        ?? $item->item?->name
                        ?? $item->item?->sku
                        ?? '—',
                    // unit may also reside on the feeding_items row
                    'unit' => $item->unit ?? $item->item?->unit ?? null,
                    'qty' => $qty,
                    'unit' => $item->unit ?? null,
                    'unit_cost' => $unitCost,
                    'total_cost' => $total,
                ];
            });
        })->values();

        $totalQty = $rows->sum('qty');
        $totalCost = $rows->sum('total_cost');
        $avgUnitCost = $totalQty > 0 ? ($totalCost / $totalQty) : 0;

        $summary = [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'total_records' => $rows->count(),
            'total_qty' => $totalQty,
            'total_cost' => $totalCost,
            'avg_unit_cost' => $avgUnitCost,
        ];

        return Inertia::render('Reports/FeedingCostAnalysis/Index', [
            'filters' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
                'inventory_item_id' => $validated['inventory_item_id'] ?? null,
                'group_by' => $groupBy,
            ],
            'items' => $items,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
