<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\InventoryItem;
use App\Models\Medicine; // Import Medicine model
use App\Models\Farm; // Import Farm model
use App\Models\StockMovement; // Import StockMovement model
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB; // Import DB facade
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Purchase::class, 'purchase');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */ // Add type hint
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $purchases = Purchase::with(['supplier', 'purchaseItems.item'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%$q%"))
                ->orWhere('invoice_number', 'like', "%$q%"))
            ->latest('purchased_at')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = Purchase::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_purchases' => (clone $baseQuery)->count(),
            'total_spent' => (clone $baseQuery)->sum('total_amount'),
            'this_month' => (clone $baseQuery)->whereMonth('purchased_at', now()->month)
                ->whereYear('purchased_at', now()->year)
                ->sum('total_amount'),
            'unique_suppliers' => (clone $baseQuery)->distinct('supplier_id')->count('supplier_id'),
        ];

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User $user */ // Add type hint
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $inventoryItems = InventoryItem::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get()->map(function ($item) {
            $item->display_name = $item->name . ' - ' . $item->unit;
            return $item;
        });

        $medicineItems = Medicine::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get()->map(function ($item) {
            $item->display_name = $item->name . ' - ' . $item->unit;
            return $item;
        });
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
            'inventoryItems' => $inventoryItems,
            'medicineItems' => $medicineItems,
            'farms' => $farms,
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
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'purchased_at' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => ['required', Rule::in(['Percent', 'Fixed'])],
            'tax' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.item_type' => ['required', Rule::in(['inventory_item', 'medicine_item'])],
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        } elseif (!empty($validated['farm_id'])) {
            // Allow non-farm-owner users (e.g. staff with permission) to create purchases for a farm
            // when farm_id is explicitly provided.
            $validated['farm_id'] = $validated['farm_id'];
        }

        DB::transaction(function () use ($validated, $user) {

            /* -------------------------------------------------
         | 1️⃣ Generate invoice number (safe enough version)
         ------------------------------------------------- */
            $lastInvoice = Purchase::lockForUpdate()
                ->whereNotNull('invoice_number')
                ->when(
                    $user->hasRole('farm owner'),
                    fn($q) => $q->where('farm_id', $user->farm_id)
                )
                ->orderByDesc('id')
                ->first();

            $lastNumber = $lastInvoice
                ? (int) substr($lastInvoice->invoice_number, 4)
                : 0;

            $invoiceNumber = 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            /* -----------------------------
         | 2️⃣ Create Purchase Header
         ----------------------------- */
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'purchased_at' => $validated['purchased_at'],
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $validated['total_amount'],
                'discount' => $validated['discount'] ?? 0,
                'discount_type' => $validated['discount_type'],
                'tax' => $validated['tax'] ?? 0,
                'tax_percentage' => $validated['tax_percentage'] ?? 0,
                'farm_id' => $validated['farm_id'] ?? null,
                'user_id' => $user->id,
            ]);

            /* ----------------------------------
         | 3️⃣ Process Purchase Line Items
         ---------------------------------- */
            $totalInventoryPurchases = 0.0;
            $totalMedicinePurchases = 0.0;

            // Category-wise totals for inventory items
            $inventoryCategoryTotals = [
                'Animal Feed' => 0.0,
                'Farm Equipment' => 0.0,
                'Veterinary Supplies' => 0.0,
            ];

            foreach ($validated['items'] as $index => $item) {

                // Enforce medicine batch & expiry
                if ($item['item_type'] === 'medicine_item') {
                    if (empty($item['batch_no']) || empty($item['expiry_date'])) {
                        throw new \Exception("Batch and expiry are required for medicine items.");
                    }
                }

                // Validate item ownership
                $itemModel = $item['item_type'] === 'inventory_item'
                    ? InventoryItem::select(
                        'sku',
                        'name',
                        'inventory_category_id',
                        'quantity',
                        'unit',
                        'min_quantity',
                        'unit_cost',
                        'supplier_id',
                        'notes',
                        'farm_id',
                        'user_id'
                    )->with('category')
                    ->where('id', $item['item_id'])
                    ->when(
                        $user->hasRole('farm owner'),
                        fn($q) => $q->where('farm_id', $user->farm_id)
                    )
                    ->first()
                    : Medicine::where('id', $item['item_id'])
                    ->when(
                        $user->hasRole('farm owner'),
                        fn($q) => $q->where('farm_id', $user->farm_id)
                    )
                    ->first();

                if (!$itemModel) {
                    throw new \Exception("Invalid item selected.");
                }

                // Always calculate subtotal server-side
                $subTotal = $item['quantity'] * $item['unit_price'];

                if ($item['item_type'] === 'inventory_item') {
                    $totalInventoryPurchases += (float) $subTotal;

                    // Add to category-wise totals (for accounting integration)
                    $categoryName = optional($itemModel->category)->name;

                    if (is_string($categoryName) && array_key_exists($categoryName, $inventoryCategoryTotals)) {
                        $inventoryCategoryTotals[$categoryName] += (float) $subTotal;
                    }
                } elseif ($item['item_type'] === 'medicine_item') {
                    $totalMedicinePurchases += (float) $subTotal;
                }

                // Create purchase item
                $purchase->purchaseItems()->create([
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'sub_total' => $subTotal,
                ]);

                // Create stock movement (IN)
                StockMovement::create([
                    'farm_id' => $purchase->farm_id,
                    'item_type' => $item['item_type'] === 'inventory_item'
                        ? InventoryItem::class
                        : Medicine::class,
                    'item_id' => $item['item_id'],
                    'movement_type' => 'in',
                    'source_type' => Purchase::class,
                    'source_event_type' => 'purchase',
                    'source_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_price'], // ✅ correct source
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'movement_date' => $purchase->purchased_at,
                    'user_id' => $user->id,
                ]);
            }

            // Auto journal integration for purchases (Inventory/Medicine vs Accounts Payable)
            $apAccount = ChartOfAccount::query()
                ->where('farm_id', $purchase->farm_id)
                ->where('code', '2001')
                ->first();

            if (!$apAccount) {
                throw new \Exception('Chart of account 2001 (Accounts Payable) not found.');
            }

            if ($totalInventoryPurchases > 0) {
                // Category-wise inventory accounts mapping
                $inventoryAccountCodesByCategory = [
                    'Animal Feed' => '1005', // Feed Inventory
                    'Farm Equipment' => '1010', // Farm Equipment Inventory
                    'Veterinary Supplies' => '1009', // Veterinary Supplies Inventory
                ];

                foreach ($inventoryCategoryTotals as $categoryName => $categoryTotal) {
                    if ($categoryTotal <= 0) {
                        continue;
                    }

                    $accountCode = $inventoryAccountCodesByCategory[$categoryName] ?? null;
                    if (!$accountCode) {
                        continue;
                    }

                    $inventoryAccount = ChartOfAccount::query()
                        ->where('farm_id', $purchase->farm_id)
                        ->where('code', $accountCode)
                        ->first();

                    if (!$inventoryAccount) {
                        throw new \Exception("Chart of account {$accountCode} ({$categoryName} Inventory) not found.");
                    }

                    $entry = JournalEntry::query()->create([
                        'farm_id' => $purchase->farm_id,
                        'user_id' => $purchase->user_id,
                        'entry_date' => $purchase->purchased_at,
                        'reference_type' => 'purchase',
                        'reference_id' => $purchase->id,
                        'description' => 'Purchase invoice ' . $purchase->invoice_number . ' (' . $categoryName . ')',
                        'status' => 'posted',
                        'created_by' => $purchase->user_id,
                    ]);

                    $entry->lines()->createMany([
                        [
                            'account_id' => $inventoryAccount->id,
                            'debit_amount' => $categoryTotal,
                            'credit_amount' => 0,
                            'narration' => $categoryName . ' Inventory',
                        ],
                        [
                            'account_id' => $apAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $categoryTotal,
                            'narration' => 'Accounts Payable',
                        ],
                    ]);
                }
            }

            if ($totalMedicinePurchases > 0) {
                $medicineInventoryAccount = ChartOfAccount::query()
                    ->where('farm_id', $purchase->farm_id)
                    ->where('code', '1008')
                    ->first();

                if (!$medicineInventoryAccount) {
                    throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                }

                $entry = JournalEntry::query()->create([
                    'farm_id' => $purchase->farm_id,
                    'user_id' => $purchase->user_id,
                    'entry_date' => $purchase->purchased_at,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'description' => 'Purchase invoice ' . $purchase->invoice_number . ' (Medicine Items)',
                    'status' => 'posted',
                    'created_by' => $purchase->user_id,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_id' => $medicineInventoryAccount->id,
                        'debit_amount' => $totalMedicinePurchases,
                        'credit_amount' => 0,
                        'narration' => 'Medicine Inventory',
                    ],
                    [
                        'account_id' => $apAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $totalMedicinePurchases,
                        'narration' => 'Accounts Payable',
                    ],
                ]);
            }
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase recorded successfully.');
    }


    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'purchaseItems.item']);
        return Inertia::render('Purchases/Show', ['purchase' => $purchase]);
    }

    public function printInvoice(Purchase $purchase)
    {
        $purchase->load(['supplier', 'purchaseItems.item', 'farm']); // Load farm for invoice details
        return Inertia::render('Purchases/PrintInvoice', ['purchase' => $purchase]);
    }

    public function edit(Purchase $purchase)
    {
        /** @var \App\Models\User $user */ // Add type hint
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $suppliers = Supplier::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $inventoryItems = InventoryItem::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get()->map(function ($item) {
            $item->display_name = $item->name . ' - ' . $item->unit;
            return $item;
        });
        $medicineItems = Medicine::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get()->map(function ($item) {
            $item->display_name = $item->name . ' - ' . $item->unit;
            return $item;
        });
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $purchase->load(['purchaseItems.item']);

        // Format the purchased_at date for the HTML date input.
        // Since it's cast to 'date' in the model, it's already a Carbon instance.
        $purchase->purchased_at = $purchase->purchased_at->format('Y-m-d');

        return Inertia::render('Purchases/Edit', [
            'purchase' => $purchase,
            'suppliers' => $suppliers,
            'inventoryItems' => $inventoryItems,
            'medicineItems' => $medicineItems,
            'farms' => $farms,
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'purchased_at' => 'required|date',
            'notes' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => ['required', Rule::in(['Percent', 'Fixed'])],
            'tax' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_items,id',
            'items.*.item_type' => ['required', Rule::in(['inventory_item', 'medicine_item'])],
            'items.*.item_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);


        try {
            DB::transaction(function () use ($validated, $purchase, $user) {
                $oldTotalAmount = (float) $purchase->total_amount;

                /** STEP 1: LOCK purchase items if stock consumed */
                foreach ($purchase->purchaseItems as $oldItem) {
                    $consumedQty = StockMovement::where('source_type', '!=', Purchase::class)
                        ->where('item_id', $oldItem->item_id)
                        ->where('batch_no', $oldItem->batch_no)
                        ->sum('quantity');

                    if ($consumedQty < 0) {
                        throw new \Exception(
                            "Cannot update purchase. Item {$oldItem->item_id} has already been consumed."
                        );
                    }
                }

                /** STEP 2: Reverse previous stock IN */
                StockMovement::where('source_type', Purchase::class)
                    ->where('source_id', $purchase->id)
                    ->delete();

                /** STEP 3: Remove old purchase items */
                $purchase->purchaseItems()->delete();

                /** STEP 4: Update purchase header */

                $validated['farm_id'] = $purchase->farm_id;
                $validated['user_id'] = $user->id;


                $purchase->update([
                    'supplier_id' => $validated['supplier_id'] ?? null,
                    'purchased_at' => $validated['purchased_at'],
                    'notes' => $validated['notes'] ?? null,
                    'total_amount' => $validated['total_amount'],
                    'discount' => $validated['discount'] ?? 0,
                    'discount_type' => $validated['discount_type'],
                    'tax' => $validated['tax'] ?? 0,
                    'tax_percentage' => $validated['tax_percentage'] ?? 0,
                    'farm_id' => $validated['farm_id'] ?? null,
                    'user_id' => $validated['user_id'] ?? null,
                ]);

                $newTotalAmount = (float) $purchase->total_amount;
                $deltaTotalAmount = $newTotalAmount - $oldTotalAmount;

                /** STEP 5: Re-insert purchase items + stock IN */
                $totalInventoryPurchases = 0.0;
                $totalMedicinePurchases = 0.0;

                foreach ($validated['items'] as $item) {

                    if (
                        $item['item_type'] === 'medicine_item' &&
                        (empty($item['batch_no']) || empty($item['expiry_date']))
                    ) {
                        throw new \Exception('Medicine items require batch and expiry.');
                    }

                    $subTotal = $item['quantity'] * $item['unit_price'];

                    if ($item['item_type'] === 'inventory_item') {
                        $totalInventoryPurchases += (float) $subTotal;
                    } elseif ($item['item_type'] === 'medicine_item') {
                        $totalMedicinePurchases += (float) $subTotal;
                    }

                    $purchaseItem = $purchase->purchaseItems()->create([
                        'item_type' => $item['item_type'],
                        'item_id' => $item['item_id'],
                        'batch_no' => $item['batch_no'] ?? null,
                        'expiry_date' => $item['expiry_date'] ?? null,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'sub_total' => $subTotal,
                    ]);

                    StockMovement::create([
                        'farm_id' => $validated['farm_id'] ?? null,
                        'item_type' => $item['item_type'] === 'inventory_item'
                            ? InventoryItem::class
                            : Medicine::class,
                        'item_id' => $item['item_id'],
                        'movement_type' => 'in',
                        'source_type' => Purchase::class,
                        'source_event_type' => 'purchase',
                        'source_id' => $purchase->id,
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_price'], // ✅ correct
                        'batch_no' => $item['batch_no'] ?? null,
                        'expiry_date' => $item['expiry_date'] ?? null,
                        'movement_date' => $purchase->purchased_at,
                        'user_id' => $validated['user_id'] ?? null,
                    ]);
                }

                // Insert adjustment journal(s) for purchase edit (delta-based, category-wise for inventory items)
                if ($deltaTotalAmount != 0.0) {
                    $apAccount = ChartOfAccount::query()
                        ->where('farm_id', $purchase->farm_id)
                        ->where('code', '2001')
                        ->first();

                    if (!$apAccount) {
                        throw new \Exception('Chart of account 2001 (Accounts Payable) not found.');
                    }

                    $medicineInventoryAccount = ChartOfAccount::query()
                        ->where('farm_id', $purchase->farm_id)
                        ->where('code', '1008')
                        ->first();

                    $hasInventory = collect($validated['items'])->contains(fn($i) => ($i['item_type'] ?? null) === 'inventory_item');
                    $hasMedicine = collect($validated['items'])->contains(fn($i) => ($i['item_type'] ?? null) === 'medicine_item');

                    // Category-wise totals for inventory items (current state)
                    $inventoryCategoryTotals = [
                        'Animal Feed' => 0.0,
                        'Farm Equipment' => 0.0,
                        'Veterinary Supplies' => 0.0,
                    ];

                    foreach ($validated['items'] as $i) {
                        if (($i['item_type'] ?? null) !== 'inventory_item') {
                            continue;
                        }

                        $inv = InventoryItem::select(
                            'sku',
                            'name',
                            'inventory_category_id',
                            'quantity',
                            'unit',
                            'min_quantity',
                            'unit_cost',
                            'supplier_id',
                            'notes',
                            'farm_id',
                            'user_id'
                        )->with('category')->find($i['item_id']);
                        if (!$inv) {
                            continue;
                        }

                        $catName = optional($inv->category)->name;
                        if (!is_string($catName) || !array_key_exists($catName, $inventoryCategoryTotals)) {
                            continue;
                        }

                        $inventoryCategoryTotals[$catName] += (float) ($i['quantity'] * $i['unit_price']);
                    }

                    // Allocate delta proportionally by current totals if both types exist
                    $currentTotal = (float) $totalInventoryPurchases + (float) $totalMedicinePurchases;

                    $allocInventory = 0.0;
                    $allocMedicine = 0.0;

                    if ($hasInventory && $hasMedicine && $currentTotal > 0) {
                        $allocInventory = $deltaTotalAmount * ((float) $totalInventoryPurchases / $currentTotal);
                        $allocMedicine = $deltaTotalAmount * ((float) $totalMedicinePurchases / $currentTotal);
                    } elseif ($hasInventory) {
                        $allocInventory = $deltaTotalAmount;
                    } elseif ($hasMedicine) {
                        $allocMedicine = $deltaTotalAmount;
                    }

                    $createAdjustmentEntry = function (string $label, ChartOfAccount $assetAccount, float $amount) use ($purchase, $apAccount) {
                        if ($amount == 0.0) {
                            return;
                        }

                        $abs = abs($amount);

                        $entry = JournalEntry::query()->create([
                            'farm_id' => $purchase->farm_id,
                            'user_id' => $purchase->user_id,
                            'entry_date' => $purchase->purchased_at,
                            'reference_type' => 'purchase',
                            'reference_id' => $purchase->id,
                            'description' => 'Purchase adjustment ' . $purchase->invoice_number . ' (' . $label . ')',
                            'status' => 'posted',
                            'created_by' => $purchase->user_id,
                        ]);

                        if ($amount > 0) {
                            // Increase: DR Asset, CR AP
                            $entry->lines()->createMany([
                                [
                                    'account_id' => $assetAccount->id,
                                    'debit_amount' => $abs,
                                    'credit_amount' => 0,
                                    'narration' => $label . ' (Adjustment)',
                                ],
                                [
                                    'account_id' => $apAccount->id,
                                    'debit_amount' => 0,
                                    'credit_amount' => $abs,
                                    'narration' => 'Accounts Payable (Adjustment)',
                                ],
                            ]);
                        } else {
                            // Decrease: DR AP, CR Asset
                            $entry->lines()->createMany([
                                [
                                    'account_id' => $apAccount->id,
                                    'debit_amount' => $abs,
                                    'credit_amount' => 0,
                                    'narration' => 'Accounts Payable (Adjustment)',
                                ],
                                [
                                    'account_id' => $assetAccount->id,
                                    'debit_amount' => 0,
                                    'credit_amount' => $abs,
                                    'narration' => $label . ' (Adjustment)',
                                ],
                            ]);
                        }
                    };

                    if ($allocInventory != 0.0) {
                        // Category-wise inventory accounts mapping
                        $inventoryAccountCodesByCategory = [
                            'Animal Feed' => '1005',
                            'Farm Equipment' => '1010',
                            'Veterinary Supplies' => '1009',
                        ];

                        $inventoryCurrentTotal = array_sum($inventoryCategoryTotals);
                        if ($inventoryCurrentTotal > 0) {
                            foreach ($inventoryCategoryTotals as $catName => $catTotal) {
                                if ($catTotal <= 0) {
                                    continue;
                                }

                                $portion = $allocInventory * ((float) $catTotal / (float) $inventoryCurrentTotal);
                                if ($portion == 0.0) {
                                    continue;
                                }

                                $code = $inventoryAccountCodesByCategory[$catName] ?? null;
                                if (!$code) {
                                    continue;
                                }

                                $assetAccount = ChartOfAccount::query()
                                    ->where('farm_id', $purchase->farm_id)
                                    ->where('code', $code)
                                    ->first();
                                if (!$assetAccount) {
                                    throw new \Exception("Chart of account {$code} ({$catName} Inventory) not found.");
                                }

                                $createAdjustmentEntry($catName, $assetAccount, $portion);
                            }
                        }
                    }

                    if ($allocMedicine != 0.0) {
                        if (!$medicineInventoryAccount) {
                            throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                        }
                        $createAdjustmentEntry('Medicine Items', $medicineInventoryAccount, $allocMedicine);
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }


        return redirect()
            ->route('purchases.index')
            ->with('success', 'Purchase updated successfully.');
    }


    public function destroy(Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {
                // Prevent deleting purchase if any of its stock has already been consumed (moved out)
                foreach ($purchase->purchaseItems as $oldItem) {
                    $consumedQty = StockMovement::where('source_type', '!=', Purchase::class)
                        ->where('item_id', $oldItem->item_id)
                        ->where('batch_no', $oldItem->batch_no)
                        ->sum('quantity');

                    if ($consumedQty < 0) {
                        throw new \Exception(
                            "Cannot delete purchase. Item {$oldItem->item_id} has already been consumed."
                        );
                    }
                }

                // Remove stock movements created by this purchase (IN)
                StockMovement::where('source_type', Purchase::class)
                    ->where('source_id', $purchase->id)
                    ->delete();

                // Remove related journal entries (if any) created for this purchase
                JournalEntry::query()
                    ->where('reference_type', 'purchase')
                    ->where('reference_id', $purchase->id)
                    ->delete();

                // Delete purchase items then purchase
                $purchase->purchaseItems()->delete();
                $purchase->delete();
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('purchases.index')->with('success', 'Purchase removed.');
    }
}
