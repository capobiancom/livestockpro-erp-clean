<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Farm;
use App\Models\Supplier;
use App\Models\Category; // Import Category model
use App\Models\MedicineGroup; // Import MedicineGroup model
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import DB facade
use Inertia\Inertia;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Medicine::class, 'medicine');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $medicines = Medicine::with(['supplier', 'category', 'medicineGroup'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhereHas('medicineGroup', fn($qbg) => $qbg->where('name', 'like', "%$q%"))
                ->orWhere('sku', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalItems = $medicines->total();
        $lowStockCount = Medicine::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->whereColumn('quantity', '<=', 'min_quantity')->count();
        $totalValue = Medicine::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->sum(\Illuminate\Support\Facades\DB::raw('quantity * unit_cost'));

        return Inertia::render('Medicines/Index', [
            'medicines' => $medicines,
            'filters' => $request->only('q'),
            'statistics' => [
                'total_items' => $totalItems,
                'low_stock_count' => $lowStockCount,
                'total_value' => $totalValue,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $categories = Category::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $medicineGroups = MedicineGroup::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Medicines/Create', [
            'farms' => $farms,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'medicineGroups' => $medicineGroups, // Pass medicine groups to the view
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'medicine_group_id' => 'nullable|exists:medicine_groups,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'farm_id' => 'required|exists:farms,id',
            'quantity' => 'required|integer|min:0',
            'unit' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::in(['ml', 'l', 'mg', 'g', 'dose', 'vial', 'ampoule', 'bottle', 'tablet', 'capsule', 'tube', 'jar', 'spray', 'pcs', 'strip'])],
            'min_quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'inventory_category_id' => 'nullable|exists:inventory_categories,id',
            'sku' => 'nullable|string|max:255|unique:medicines,sku',
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
            $inventoryItem = Medicine::create($validated);;

            // Record stock movement for new item (initial stock)
            if ($validated['quantity'] > 0) {
                StockMovement::create([
                    'farm_id' => $validated['farm_id'],
                    'item_type' => Medicine::class,
                    'item_id' => $inventoryItem->id,
                    'movement_type' => 'in',
                    'source_event_type' => 'adjustment', // Initial stock can be considered an adjustment
                    'source_id' => $inventoryItem->id,
                    'source_type' => Medicine::class,
                    'quantity' => $validated['quantity'],
                    'unit_cost' => $validated['unit_cost'] ?? 0,
                    'movement_date' => now()->toDateString(),
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return redirect()->route('medicines.index')->with('success', 'Medicine created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to add medicine: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Medicine $medicine)
    {
        $medicine->load('supplier', 'category'); // Eager load relationships
        return Inertia::render('Medicines/Show', ['medicine' => $medicine]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medicine $medicine)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $categories = Category::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $medicineGroups = MedicineGroup::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Medicines/Edit', [
            'medicine' => $medicine,
            'farms' => $farms,
            'suppliers' => $suppliers,
            'categories' => $categories,
            'medicineGroups' => $medicineGroups, // Pass medicine groups to the view
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'medicine_group_id' => 'nullable|exists:medicine_groups,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'farm_id' => 'required|exists:farms,id',
            'quantity' => 'required|integer|min:0',
            'unit' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::in(['ml', 'l', 'mg', 'g', 'dose', 'vial', 'ampoule', 'bottle', 'tablet', 'capsule', 'tube', 'jar', 'spray', 'pcs', 'strip'])],
            'min_quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'inventory_category_id' => 'nullable|exists:inventory_categories,id',
            'sku' => 'nullable|string|max:255|unique:medicines,sku,' . $medicine->id,
        ]);

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
            $oldQuantity = floatVal($medicine->quantity);

            $medicine->update($validated);

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
                    'farm_id' => $medicine->farm_id,
                    'item_type' => Medicine::class,
                    'item_id' => $medicine->id,
                    'movement_type' => $movementType,
                    'source_event_type' => $sourceEventType,
                    'source_id' => $medicine->id,
                    'source_type' => Medicine::class,
                    'quantity' => $quantityChange,
                    'unit_cost' => $validated['unit_cost'] ?? $medicine->unit_cost ?? 0,
                    'movement_date' => now()->toDateString(),
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return redirect()->route('medicines.show', $medicine)->with('success', 'Medicine updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update medicine: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }
}
