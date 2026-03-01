<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import the User model
use App\Models\InventoryItem; // Import InventoryItem model
use App\Models\Medicine; // Import Medicine model

class StockMovementController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StockMovement::class, 'stock_movement');
    }

    public function index(Request $request)
    {
        /** @var User $user */ // Type hint the user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $stockMovements = StockMovement::with(['item', 'source']);

        // Super admin or user with 'view stock movements' permission sees everything
        if ($user->hasRole('super_admin') || $user->can('view stock movements')) {
            // No additional query conditions needed
        }
        // Farm owner sees only their farm's stock movements
        else if ($user->hasRole('farm_owner')) {
            $stockMovements->where('farm_id', $user->farm_id);
        }
        // If none of the above, the policy will handle authorization for viewAny.
        // If viewAny is true, but no specific role/permission grants global or farm-specific access,
        // then no filtering is applied here, relying on the policy for individual record access.

        $stockMovements = $stockMovements->when($q, function ($query) use ($q) {
            $query->where('movement_type', 'like', "%$q%")
                ->orWhere('source_event_type', 'like', "%$q%")
                ->orWhereHasMorph('item', [\App\Models\InventoryItem::class, \App\Models\Medicine::class], function ($query, $type) use ($q) {
                    $query->where('name', 'like', "%$q%");
                });
        })
            ->latest('movement_date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = StockMovement::query();
        if ($user->hasRole('farm_owner')) {
            $baseQuery->where('farm_id', $user->farm_id);
        }

        // Calculate statistics
        $baseQuery = StockMovement::query();
        if ($user->hasRole('farm_owner')) {
            $baseQuery->where('farm_id', $user->farm_id);
        }

        $inventoryItemMovements = (clone $baseQuery)
            ->where('stock_movements.item_type', InventoryItem::class)
            ->join('inventory_items', 'stock_movements.item_id', '=', 'inventory_items.id')
            ->selectRaw('inventory_items.unit, stock_movements.movement_type, SUM(stock_movements.quantity) as total_quantity')
            ->groupBy('inventory_items.unit', 'stock_movements.movement_type');

        $medicineMovements = (clone $baseQuery)
            ->where('stock_movements.item_type', Medicine::class)
            ->join('medicines', 'stock_movements.item_id', '=', 'medicines.id')
            ->selectRaw('medicines.unit, stock_movements.movement_type, SUM(stock_movements.quantity) as total_quantity')
            ->groupBy('medicines.unit', 'stock_movements.movement_type');

        $combinedStockMovements = $inventoryItemMovements->unionAll($medicineMovements);

        $rawGroupedMovements = $combinedStockMovements->get();

        $stockMovementsByUnit = $rawGroupedMovements->groupBy('unit');

        $formattedStockMovements = [];
        foreach ($stockMovementsByUnit as $unit => $movements) {
            $totalIn = $movements->where('movement_type', 'in')->sum('total_quantity');
            $totalOut = $movements->where('movement_type', 'out')->sum('total_quantity');
            $formattedStockMovements[] = [
                'unit' => $unit,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
            ];
        }

        $statistics = [
            'total_movements' => (clone $baseQuery)->count(),
            'total_in' => (clone $baseQuery)->where('movement_type', 'in')->sum('quantity'),
            'total_out' => (clone $baseQuery)->where('movement_type', 'out')->sum('quantity'),
            'unit_wise_movements' => $formattedStockMovements,
        ];

        return Inertia::render('StockMovements/Index', [
            'stockMovements' => $stockMovements,
            'filters' => $request->only('q'),
            'statistics' => $statistics,
        ]);
    }

    public function show(StockMovement $stockMovement)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Authorize the action using the policy
        $this->authorize('view', $stockMovement);

        $stockMovement->load(['item', 'source', 'user', 'farm']);

        return Inertia::render('StockMovements/Show', [
            'stockMovement' => $stockMovement,
        ]);
    }

    // You can add other methods (create, store, edit, update, destroy) as needed
    // For now, stock movements are primarily auto-generated, so direct CRUD might not be necessary.
}
