<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LowStockAlertsReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('lowStockAlertReport', StockMovement::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization aligned with stock movement access patterns.
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'item_type' => ['nullable', 'string', 'in:all,inventory,medicine'],
            'only_below_min' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:name,stock,min,shortage,value'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $q = $validated['q'] ?? null;
        $itemType = $validated['item_type'] ?? 'all';
        $onlyBelowMin = (bool)($validated['only_below_min'] ?? true);
        $sort = $validated['sort'] ?? 'shortage';
        $direction = $validated['direction'] ?? 'desc';

        $base = StockMovement::query()
            ->select([
                'stock_movements.item_type',
                'stock_movements.item_id',
            ])
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) as total_in")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) as total_out")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END) as total_adjustment")
            ->groupBy('stock_movements.item_type', 'stock_movements.item_id');

        if ($user->hasRole('farm_owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        if ($itemType === 'inventory') {
            $base->where('stock_movements.item_type', InventoryItem::class);
        } elseif ($itemType === 'medicine') {
            $base->where('stock_movements.item_type', Medicine::class);
        }

        // Join item tables for name/unit/min_quantity
        $base->leftJoin('inventory_items', function ($join) {
            $join->on('inventory_items.id', '=', 'stock_movements.item_id')
                ->where('stock_movements.item_type', '=', InventoryItem::class);
        });

        $base->leftJoin('medicines', function ($join) {
            $join->on('medicines.id', '=', 'stock_movements.item_id')
                ->where('stock_movements.item_type', '=', Medicine::class);
        });

        $base->addSelect([
            DB::raw("COALESCE(inventory_items.name, medicines.name) as item_name"),
            DB::raw("COALESCE(inventory_items.sku, medicines.sku) as sku"),
            DB::raw("COALESCE(inventory_items.unit, medicines.unit) as unit"),
            DB::raw("COALESCE(inventory_items.min_quantity, medicines.min_quantity, 0) as min_quantity"),
        ]);

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('inventory_items.name', 'like', "%{$q}%")
                    ->orWhere('inventory_items.sku', 'like', "%{$q}%")
                    ->orWhere('medicines.name', 'like', "%{$q}%")
                    ->orWhere('medicines.sku', 'like', "%{$q}%");
            });
        }

        // current_stock = in - out + adjustment
        $base->addSelect([
            DB::raw("(SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)) as current_stock"),
        ]);

        // shortage = max(min - current, 0)
        // NOTE: SQLite doesn't support GREATEST(), so we use CASE for portability.
        $base->addSelect([
            DB::raw("CASE
                WHEN (COALESCE(inventory_items.min_quantity, medicines.min_quantity, 0) - (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END))) > 0
                THEN (COALESCE(inventory_items.min_quantity, medicines.min_quantity, 0) - (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)))
                ELSE 0
            END as shortage"),
        ]);

        // avg unit cost based on inbound value / inbound qty (for value-at-risk)
        $base->addSelect([
            DB::raw("CASE WHEN SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) > 0
                THEN (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0) ELSE 0 END) / SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END))
                ELSE 0 END as avg_unit_cost"),
        ]);

        $base->addSelect([
            DB::raw("((CASE
                WHEN (COALESCE(inventory_items.min_quantity, medicines.min_quantity, 0) - (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END))) > 0
                THEN (COALESCE(inventory_items.min_quantity, medicines.min_quantity, 0) - (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)))
                ELSE 0
            END) *
                (CASE WHEN SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) > 0
                    THEN (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0) ELSE 0 END) / SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END))
                    ELSE 0 END)
            ) as shortage_value"),
        ]);

        if ($onlyBelowMin) {
            $base->havingRaw('shortage > 0');
        }

        $sortMap = [
            'name' => 'item_name',
            'stock' => 'current_stock',
            'min' => 'min_quantity',
            'shortage' => 'shortage',
            'value' => 'shortage_value',
        ];

        $base->orderBy($sortMap[$sort] ?? 'shortage', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'item_type' => $r->item_type,
                'item_id' => (int)$r->item_id,
                'sku' => $r->sku,
                'name' => $r->item_name,
                'unit' => $r->unit,
                'min_quantity' => (float)$r->min_quantity,
                'current_stock' => (float)$r->current_stock,
                'shortage' => (float)$r->shortage,
                'avg_unit_cost' => round((float)$r->avg_unit_cost, 2),
                'shortage_value' => round((float)$r->shortage_value, 2),
            ];
        });

        $summary = [
            'total_alerts' => $rows->count(),
            'total_shortage' => round($rows->sum('shortage'), 2),
            'total_shortage_value' => round($rows->sum('shortage_value'), 2),
        ];

        return Inertia::render('Reports/InventoryReports/LowStockAlerts/Index', [
            'filters' => [
                'q' => $q,
                'item_type' => $itemType,
                'only_below_min' => $onlyBelowMin,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
