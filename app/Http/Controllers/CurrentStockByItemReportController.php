<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CurrentStockByItemReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('currentStockByItemReport', StockMovement::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorization: reuse existing "view stock movements" permission if present,
        // otherwise allow farm owners (consistent with StockMovementController behavior).
        if (!($user->hasRole('farm owner') || $user->hasRole('super admin') || $user->can('view stock movements'))) {
            abort(403);
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'item_type' => ['nullable', 'string', 'in:all,inventory,medicine'],
            'only_in_stock' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'string', 'in:name,stock,unit_cost,value'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $q = $validated['q'] ?? null;
        $itemType = $validated['item_type'] ?? 'all';
        $onlyInStock = (bool)($validated['only_in_stock'] ?? false);
        $sort = $validated['sort'] ?? 'name';
        $direction = $validated['direction'] ?? 'asc';

        $base = StockMovement::query()
            ->select([
                'stock_movements.item_type',
                'stock_movements.item_id',
            ])
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) as total_in")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) as total_out")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END) as total_adjustment")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0) ELSE 0 END) as total_in_value")
            ->groupBy('stock_movements.item_type', 'stock_movements.item_id');

        if ($user->hasRole('farm_owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        if ($itemType === 'inventory') {
            $base->where('stock_movements.item_type', InventoryItem::class);
        } elseif ($itemType === 'medicine') {
            $base->where('stock_movements.item_type', Medicine::class);
        }

        // Join item names/units for filtering + display
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
        ]);

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('inventory_items.name', 'like', "%{$q}%")
                    ->orWhere('inventory_items.sku', 'like', "%{$q}%")
                    ->orWhere('medicines.name', 'like', "%{$q}%")
                    ->orWhere('medicines.sku', 'like', "%{$q}%");
            });
        }

        // stock = in - out + adjustment
        $base->addSelect([
            DB::raw("(SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)) as current_stock"),
        ]);

        if ($onlyInStock) {
            $base->havingRaw('current_stock > 0');
        }

        // avg unit cost based on inbound value / inbound qty
        $base->addSelect([
            DB::raw("CASE WHEN SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) > 0
                THEN (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0) ELSE 0 END) / SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END))
                ELSE 0 END as avg_unit_cost"),
        ]);

        $base->addSelect([
            DB::raw("((SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)) *
                (CASE WHEN SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) > 0
                    THEN (SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0) ELSE 0 END) / SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END))
                    ELSE 0 END)
            ) as stock_value"),
        ]);

        $sortMap = [
            'name' => 'item_name',
            'stock' => 'current_stock',
            'unit_cost' => 'avg_unit_cost',
            'value' => 'stock_value',
        ];

        $base->orderBy($sortMap[$sort] ?? 'item_name', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'item_type' => $r->item_type,
                'item_id' => (int)$r->item_id,
                'sku' => $r->sku,
                'name' => $r->item_name,
                'unit' => $r->unit,
                'total_in' => (float)$r->total_in,
                'total_out' => (float)$r->total_out,
                'total_adjustment' => (float)$r->total_adjustment,
                'current_stock' => (float)$r->current_stock,
                'avg_unit_cost' => round((float)$r->avg_unit_cost, 2),
                'stock_value' => round((float)$r->stock_value, 2),
            ];
        });

        $summary = [
            'total_items' => $rows->count(),
            'total_stock' => round($rows->sum('current_stock'), 2),
            'total_value' => round($rows->sum('stock_value'), 2),
        ];

        return Inertia::render('Reports/InventoryReports/CurrentStockByItem/Index', [
            'filters' => [
                'q' => $q,
                'item_type' => $itemType,
                'only_in_stock' => $onlyInStock,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
