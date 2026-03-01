<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ExpiredMedicineReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('expiredMedicineAlertReport', StockMovement::class);

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
            'status' => ['nullable', 'string', 'in:expired,expiring_soon'],
            'days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'sort' => ['nullable', 'string', 'in:expiry_date,name,stock,value'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
        ]);

        $q = $validated['q'] ?? null;
        $status = $validated['status'] ?? 'expired';
        $days = (int)($validated['days'] ?? 30);
        $sort = $validated['sort'] ?? 'expiry_date';
        $direction = $validated['direction'] ?? 'asc';

        $today = now()->toDateString();
        $soonDate = now()->addDays($days)->toDateString();

        $base = StockMovement::query()
            ->where('stock_movements.item_type', Medicine::class)
            ->whereNotNull('stock_movements.expiry_date')
            ->select([
                'stock_movements.item_id',
                'stock_movements.batch_no',
                'stock_movements.expiry_date',
            ])
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) as total_in")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) as total_out")
            ->selectRaw("SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END) as total_adjustment")
            ->groupBy('stock_movements.item_id', 'stock_movements.batch_no', 'stock_movements.expiry_date');

        if ($user->hasRole('farm_owner')) {
            $base->where('stock_movements.farm_id', $user->farm_id);
        }

        $base->leftJoin('medicines', 'medicines.id', '=', 'stock_movements.item_id');

        $base->addSelect([
            DB::raw('medicines.name as medicine_name'),
            DB::raw('medicines.sku as sku'),
            DB::raw('medicines.unit as unit'),
        ]);

        if (!empty($q)) {
            $base->where(function ($qb) use ($q) {
                $qb->where('medicines.name', 'like', "%{$q}%")
                    ->orWhere('medicines.sku', 'like', "%{$q}%")
                    ->orWhere('stock_movements.batch_no', 'like', "%{$q}%");
            });
        }

        // current_stock = in - out + adjustment
        $base->addSelect([
            DB::raw("(SUM(CASE WHEN stock_movements.movement_type = 'in' THEN stock_movements.quantity ELSE 0 END) - SUM(CASE WHEN stock_movements.movement_type = 'out' THEN stock_movements.quantity ELSE 0 END) + SUM(CASE WHEN stock_movements.movement_type = 'adjustment' THEN stock_movements.quantity ELSE 0 END)) as current_stock"),
        ]);

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

        // Only show batches that still have stock
        $base->havingRaw('current_stock > 0');

        if ($status === 'expired') {
            $base->whereDate('stock_movements.expiry_date', '<', $today);
        } else {
            $base->whereDate('stock_movements.expiry_date', '>=', $today)
                ->whereDate('stock_movements.expiry_date', '<=', $soonDate);
        }

        $sortMap = [
            'expiry_date' => 'stock_movements.expiry_date',
            'name' => 'medicine_name',
            'stock' => 'current_stock',
            'value' => 'stock_value',
        ];

        $base->orderBy($sortMap[$sort] ?? 'stock_movements.expiry_date', $direction);

        $rows = $base->limit(5000)->get()->map(function ($r) {
            return [
                'medicine_id' => (int)$r->item_id,
                'sku' => $r->sku,
                'name' => $r->medicine_name,
                'unit' => $r->unit,
                'batch_no' => $r->batch_no,
                'expiry_date' => optional($r->expiry_date)->toDateString(),
                'current_stock' => (float)$r->current_stock,
                'avg_unit_cost' => round((float)$r->avg_unit_cost, 2),
                'stock_value' => round((float)$r->stock_value, 2),
            ];
        });

        $summary = [
            'total_batches' => $rows->count(),
            'total_stock' => round($rows->sum('current_stock'), 2),
            'total_value' => round($rows->sum('stock_value'), 2),
        ];

        return Inertia::render('Reports/InventoryReports/ExpiredMedicine/Index', [
            'filters' => [
                'q' => $q,
                'status' => $status,
                'days' => $days,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }
}
