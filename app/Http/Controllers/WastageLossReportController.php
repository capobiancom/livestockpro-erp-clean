<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WastageLossReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('wastageAndLossReport', StockMovement::class);

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
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'sort' => ['nullable', 'string', 'in:name,qty,value,wastage_pct'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $q = $validated['q'] ?? null;
        $itemType = $validated['item_type'] ?? 'all';
        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;
        $sort = $validated['sort'] ?? 'value';
        $direction = $validated['direction'] ?? 'desc';

        // Base query: loss movements only
        $base = StockMovement::query()
            ->select([
                'stock_movements.item_type',
                'stock_movements.item_id',
            ])
            ->where('stock_movements.movement_type', 'loss')
            ->selectRaw('SUM(stock_movements.quantity) as loss_qty')
            ->selectRaw('SUM(stock_movements.quantity * COALESCE(stock_movements.unit_cost, 0)) as loss_value')
            ->groupBy('stock_movements.item_type', 'stock_movements.item_id');

        if ($user->hasRole('farm_owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        if (!empty($from)) {
            $base->whereDate('stock_movements.movement_date', '>=', $from);
        }
        if (!empty($to)) {
            $base->whereDate('stock_movements.movement_date', '<=', $to);
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
            DB::raw('COALESCE(inventory_items.name, medicines.name) as item_name'),
            DB::raw('COALESCE(inventory_items.sku, medicines.sku) as sku'),
            DB::raw('COALESCE(inventory_items.unit, medicines.unit) as unit'),
        ]);

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('inventory_items.name', 'like', "%{$q}%")
                    ->orWhere('inventory_items.sku', 'like', "%{$q}%")
                    ->orWhere('medicines.name', 'like', "%{$q}%")
                    ->orWhere('medicines.sku', 'like', "%{$q}%");
            });
        }

        // Advanced: wastage % = (loss qty / total purchased qty) * 100
        // Purchased qty is based on inbound movements (movement_type = 'in') for the same item.
        // We compute it via a correlated subquery to avoid complex joins.
        $base->addSelect([
            DB::raw("(
                SELECT COALESCE(SUM(sm_in.quantity), 0)
                FROM stock_movements sm_in
                WHERE sm_in.item_type = stock_movements.item_type
                  AND sm_in.item_id = stock_movements.item_id
                  AND sm_in.movement_type = 'in'
                  " . ($user->hasRole('farm_owner') ? "AND sm_in.farm_id = " . (int)$user->farm_id : "") . "
                  " . (!empty($from) ? "AND DATE(sm_in.movement_date) >= '" . addslashes($from) . "'" : "") . "
                  " . (!empty($to) ? "AND DATE(sm_in.movement_date) <= '" . addslashes($to) . "'" : "") . "
            ) as purchased_qty"),
        ]);

        $base->addSelect([
            DB::raw("CASE
                WHEN (
                    SELECT COALESCE(SUM(sm_in.quantity), 0)
                    FROM stock_movements sm_in
                    WHERE sm_in.item_type = stock_movements.item_type
                      AND sm_in.item_id = stock_movements.item_id
                      AND sm_in.movement_type = 'in'
                      " . ($user->hasRole('farm_owner') ? "AND sm_in.farm_id = " . (int)$user->farm_id : "") . "
                      " . (!empty($from) ? "AND DATE(sm_in.movement_date) >= '" . addslashes($from) . "'" : "") . "
                      " . (!empty($to) ? "AND DATE(sm_in.movement_date) <= '" . addslashes($to) . "'" : "") . "
                ) > 0
                THEN (SUM(stock_movements.quantity) / (
                    SELECT COALESCE(SUM(sm_in.quantity), 0)
                    FROM stock_movements sm_in
                    WHERE sm_in.item_type = stock_movements.item_type
                      AND sm_in.item_id = stock_movements.item_id
                      AND sm_in.movement_type = 'in'
                      " . ($user->hasRole('farm_owner') ? "AND sm_in.farm_id = " . (int)$user->farm_id : "") . "
                      " . (!empty($from) ? "AND DATE(sm_in.movement_date) >= '" . addslashes($from) . "'" : "") . "
                      " . (!empty($to) ? "AND DATE(sm_in.movement_date) <= '" . addslashes($to) . "'" : "") . "
                )) * 100
                ELSE 0
            END as wastage_pct"),
        ]);

        $sortMap = [
            'name' => 'item_name',
            'qty' => 'loss_qty',
            'value' => 'loss_value',
            'wastage_pct' => 'wastage_pct',
        ];

        $base->orderBy($sortMap[$sort] ?? 'loss_value', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'item_type' => $r->item_type,
                'item_id' => (int)$r->item_id,
                'sku' => $r->sku,
                'name' => $r->item_name,
                'unit' => $r->unit,
                'loss_qty' => round((float)$r->loss_qty, 2),
                'loss_value' => round((float)$r->loss_value, 2),
                'purchased_qty' => round((float)$r->purchased_qty, 2),
                'wastage_pct' => round((float)$r->wastage_pct, 2),
            ];
        });

        $summary = [
            'total_items' => $rows->count(),
            'total_loss_qty' => round($rows->sum('loss_qty'), 2),
            'total_loss_value' => round($rows->sum('loss_value'), 2),
            'avg_wastage_pct' => $rows->count() > 0 ? round($rows->avg('wastage_pct'), 2) : 0,
        ];

        return Inertia::render('Reports/InventoryReports/WastageLoss/Index', [
            'filters' => [
                'q' => $q,
                'item_type' => $itemType,
                'from' => $from,
                'to' => $to,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
