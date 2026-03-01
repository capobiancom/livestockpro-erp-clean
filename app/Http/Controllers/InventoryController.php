<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\Farm; // Import Farm model
use App\Models\Category; // Import Category model
use App\Models\StockMovement; // Import StockMovement model
use App\Data\InventoryItemData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(InventoryItem::class, 'inventory');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $items = InventoryItem::with(['supplier', 'category'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhere('sku', 'like', "%$q%")
                ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%$q%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = InventoryItem::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $totalItems = (clone $baseQuery)->count();
        $lowStockCount = (clone $baseQuery)->whereRaw('quantity <= min_quantity')->count();
        $totalValue = (clone $baseQuery)->whereNotNull('unit_cost')
            ->whereNotNull('quantity')
            ->selectRaw('SUM(quantity * unit_cost) as total')
            ->value('total') ?? 0;

        return Inertia::render('Inventory/Index', [
            'items' => $items,
            'filters' => $request->only('q'),
            'statistics' => [
                'total_items' => $totalItems,
                'low_stock_count' => $lowStockCount,
                'total_value' => round($totalValue, 2)
            ]
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        $categories = Category::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Inventory/Create', [
            'suppliers' => $suppliers,
            'farms' => $farms,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => [
                'nullable',
                'string',
                Rule::unique('inventory_items', 'sku')
                    ->where(fn($q) => $q->where('farm_id', $user->farm_id)),
            ],
            'inventory_category_id' => 'nullable|exists:inventory_categories,id',
            'quantity' => 'nullable|numeric',
            'unit' => ['nullable', 'string', \Illuminate\Validation\Rule::in(['kg', 'quintal', 'ton', 'liter', 'gallon', 'pcs', 'pair', 'bundle', 'roll', 'bag', 'sack', 'meter', 'feet', 'sqft', 'sqm', 'bale', 'packet', 'box', 'carton'])],
            'min_quantity' => 'nullable|numeric',
            'unit_cost' => 'nullable|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }
        // Validate that the selected supplier belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['supplier_id'])) {
            $supplier = Supplier::where('id', $validated['supplier_id'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$supplier) {
                return back()->withErrors(['supplier_id' => 'The selected supplier does not belong to your farm.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $data = InventoryItemData::from($validated);
            $inventoryItem = InventoryItem::create($data->toArray());

            // Record stock movement for new item (initial stock)
            if ($validated['quantity'] > 0) {

                StockMovement::create([
                    'farm_id' => $user->farm_id,
                    'item_type' => InventoryItem::class,
                    'item_id' => $inventoryItem->id,
                    'movement_type' => 'in',
                    'source_event_type' => 'adjustment', // Initial stock can be considered an adjustment
                    'source_id' => $inventoryItem->id,
                    'source_type' => InventoryItem::class,
                    'quantity' => $validated['quantity'],
                    'unit_cost' => $validated['unit_cost'] ?? 0,
                    'movement_date' => now()->toDateString(),
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Item added');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add item: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(InventoryItem $inventory)
    {
        $this->authorize('view', $inventory);
        $inventory->load(['supplier', 'category']);
        return Inertia::render('Inventory/Show', ['item' => $inventory]);
    }

    public function edit(InventoryItem $inventory)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $inventory->load(['supplier', 'category']);
        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        $categories = Category::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Inventory/Edit', [
            'item' => $inventory,
            'suppliers' => $suppliers,
            'farms' => $farms,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'sku' => [
                'nullable',
                'string',
                Rule::unique('inventory_items', 'sku')
                    ->ignore($inventory->id)
                    ->where(fn($q) => $q->where('farm_id', $user->farm_id)),
            ],
            'inventory_category_id' => 'nullable|exists:inventory_categories,id',
            'quantity' => 'nullable|numeric',
            'unit' => ['nullable', 'string', \Illuminate\Validation\Rule::in(['kg', 'quintal', 'ton', 'liter', 'gallon', 'pcs', 'pair', 'bundle', 'roll', 'bag', 'sack', 'meter', 'feet', 'sqft', 'sqm', 'bale', 'packet', 'box', 'carton'])],
            'min_quantity' => 'nullable|numeric',
            'unit_cost' => 'nullable|numeric',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }
        // Validate that the selected supplier belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['supplier_id'])) {
            $supplier = Supplier::where('id', $validated['supplier_id'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$supplier) {
                return back()->withErrors(['supplier_id' => 'The selected supplier does not belong to your farm.'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Get old quantity for stock adjustment
            $oldQuantity = floatVal($inventory->quantity);

            $data = InventoryItemData::from($validated);
            $inventory->update($data->toArray());

            // Record stock movement if quantity changed
            $newQuantity = floatVal($validated['quantity']) ?? 0;
            if ($newQuantity !== $oldQuantity) {
                $movementType = '';
                $quantityChange = abs($newQuantity - $oldQuantity);

                if ($newQuantity > $oldQuantity) {
                    $movementType = 'in';
                    $sourceEventType = 'adjustment'; // Assuming increase is due to a "purchase" or similar inflow
                } else {
                    $movementType = 'out';
                    $sourceEventType = 'loss'; // Assuming decrease is due to a "loss" or similar outflow
                }

                StockMovement::create([
                    'farm_id' => $user->farm_id,
                    'item_type' => InventoryItem::class,
                    'item_id' => $inventory->id,
                    'movement_type' => $movementType,
                    'source_event_type' => $sourceEventType,
                    'source_id' => $inventory->id,
                    'source_type' => InventoryItem::class,
                    'quantity' => $quantityChange,
                    'unit_cost' => $validated['unit_cost'] ?? $inventory->unit_cost ?? 0,
                    'movement_date' => now()->toDateString(),
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.show', $inventory)->with('success', 'Item updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update item: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(InventoryItem $inventory)
    {

        DB::beginTransaction();
        try {
            // Record stock movement for deletion (loss)
            if ($inventory->quantity > 0) {
                StockMovement::create([
                    'farm_id' => $inventory->farm_id,
                    'item_type' => InventoryItem::class,
                    'item_id' => $inventory->id,
                    'movement_type' => 'out',
                    'source_event_type' => 'loss', // Item removed from inventory
                    'source_id' => $inventory->id,
                    'source_type' => InventoryItem::class,
                    'quantity' => $inventory->quantity,
                    'unit_cost' => $inventory->unit_cost ?? 0,
                    'movement_date' => now()->toDateString(),
                    'user_id' => Auth::id(),
                ]);
            }

            $inventory->delete();
            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Item deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete item: ' . $e->getMessage()])->withInput();
        }
    }
}
