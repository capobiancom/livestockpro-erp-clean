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

        $items = InventoryItem::query()
            ->select(['id', 'name', 'unit'])
            ->orderBy('name')
            ->limit(1000)
            ->get();

        $query = FeedingRecord::query()
            ->with([
                'feedingItems:id,feeding_record_id,name,unit,quantity,unit_cost',
                'animal:id,tag_number,name',
            ])
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
                    'animal' => trim(($record->animal?->tag_number ?? '') . ' ' . ($record->animal?->name ?? '')),
                    'item' => $item->name ?? '—',
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
