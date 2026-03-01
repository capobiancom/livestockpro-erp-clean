<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MonthlyConsumptionSummaryReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('monthlyConsumptionSummeryReport', StockMovement::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization aligned with inventory report patterns.
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'item_type' => ['nullable', 'string', 'in:inventory,medicine'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'in:item,quantity,cost'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $month = $validated['month'] ?? now()->format('Y-m');
        $itemType = $validated['item_type'] ?? 'inventory';
        $q = $validated['q'] ?? null;
        $sort = $validated['sort'] ?? 'cost';
        $direction = $validated['direction'] ?? 'desc';

        $from = now()->setDate((int)substr($month, 0, 4), (int)substr($month, 5, 2), 1)->startOfMonth()->toDateString();
        $to = now()->setDate((int)substr($month, 0, 4), (int)substr($month, 5, 2), 1)->endOfMonth()->toDateString();

        $itemModel = $itemType === 'medicine' ? \App\Models\Medicine::class : InventoryItem::class;

        $base = StockMovement::query()
            ->where('stock_movements.movement_type', 'out')
            ->where('stock_movements.item_type', $itemModel)
            ->whereDate('stock_movements.movement_date', '>=', $from)
            ->whereDate('stock_movements.movement_date', '<=', $to);

        if ($user->hasRole('farm owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        // Join items table for name/unit display
        if ($itemType === 'medicine') {
            $base->leftJoin('medicines', 'medicines.id', '=', 'stock_movements.item_id')
                ->addSelect([
                    'stock_movements.item_id as item_id',
                    DB::raw("'medicine' as item_type"),
                    DB::raw('COALESCE(medicines.name, "Unknown") as item_name'),
                    DB::raw('COALESCE(medicines.unit, "") as unit'),
                ]);
        } else {
            $base->leftJoin('inventory_items', 'inventory_items.id', '=', 'stock_movements.item_id')
                ->addSelect([
                    'stock_movements.item_id as item_id',
                    DB::raw("'inventory' as item_type"),
                    DB::raw('COALESCE(inventory_items.name, "Unknown") as item_name'),
                    DB::raw('COALESCE(inventory_items.unit, "") as unit'),
                ]);
        }

        if (!empty($q)) {
            $base->where(function ($qb) use ($q, $itemType) {
                if ($itemType === 'medicine') {
                    $qb->where('medicines.name', 'like', "%{$q}%");
                } else {
                    $qb->where('inventory_items.name', 'like', "%{$q}%");
                }
            });
        }

        $base->selectRaw('SUM(stock_movements.quantity) as total_quantity')
            ->selectRaw('SUM(COALESCE(stock_movements.unit_cost, 0) * COALESCE(stock_movements.quantity, 0)) as total_cost')
            ->groupBy('stock_movements.item_id', 'item_name', 'unit');

        $sortMap = [
            'item' => 'item_name',
            'quantity' => 'total_quantity',
            'cost' => 'total_cost',
        ];

        $base->orderBy($sortMap[$sort] ?? 'total_cost', $direction);

        $rows = $base->limit(10000)->get()->map(function ($r) {
            return [
                'item_id' => (int)$r->item_id,
                'item_type' => $r->item_type,
                'item_name' => $r->item_name,
                'unit' => $r->unit,
                'total_quantity' => round((float)$r->total_quantity, 2),
                'total_cost' => round((float)$r->total_cost, 2),
            ];
        });

        $summary = [
            'month' => $month,
            'total_items' => $rows->count(),
            'total_quantity' => round($rows->sum('total_quantity'), 2),
            'total_cost' => round($rows->sum('total_cost'), 2),
        ];

        return Inertia::render('Reports/InventoryReports/MonthlyConsumptionSummary/Index', [
            'filters' => [
                'month' => $month,
                'item_type' => $itemType,
                'q' => $q,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
