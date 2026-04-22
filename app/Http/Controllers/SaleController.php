<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Farm;
use App\Enums\SaleStatus;
use App\Models\InventoryItem;
use App\Models\Animal;
use App\Models\MilkSale;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User; // Import User model for type hinting
use App\Enums\AnimalStatus;
use App\Models\SaleTransaction; // Import SaleTransaction model
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sale::class, 'sale');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $q = $request->input('q');
        $sales = Sale::with(['customer', 'salesItems.item', 'saleTransactions']) // Load saleTransactions
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")))
            ->latest('invoice_date')
            ->paginate(20)
            ->withQueryString();

        $baseQuery = Sale::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_sales' => (clone $baseQuery)->count(),
            'total_revenue' => (clone $baseQuery)->sum('total_amount'),
            'this_month' => (clone $baseQuery)->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('total_amount'),
            'this_year' => (clone $baseQuery)->whereYear('invoice_date', now()->year)
                ->sum('total_amount'),
        ];

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
            'currency' => \App\Models\Setting::first()->currency ?? 'BDT', // Fetch currency from settings, default to 'BDT'
        ]);
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $customers = Customer::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $inventoryItems = InventoryItem::select('id', 'name', 'unit', 'farm_id')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get()
            ->map(function ($item) {
                $item->unit_price = $item->fifo_unit_price; // Use the accessor
                return $item;
            });


        $animals = Animal::select('id', 'name', 'tag', 'current_weight_kg', 'farm_id')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get()
            ->map(function ($animal) {
                $animal->unit_price = (float) StockMovement::query()
                    ->where('farm_id', $animal->farm_id)
                    ->where('item_type', Animal::class)
                    ->where('item_id', $animal->id)
                    ->where('movement_type', 'in')
                    ->latest('movement_date')
                    ->value('unit_cost') ?? 0;

                return $animal;
            });

        $saleStatuses = collect(SaleStatus::cases())->map(function ($status) {
            return ['value' => $status->value, 'name' => $status->name];
        })->toArray();

        return Inertia::render('Sales/Create', [
            'customers' => $customers,
            'inventoryItems' => $inventoryItems,
            'animals' => $animals,
            'saleStatuses' => $saleStatuses,
        ]);
    }

    public function store(StoreSaleRequest $request)
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        try {
            DB::transaction(function () use ($validated, $user) {
                $validated['user_id'] = $user->id;
                $validated['farm_id'] = $user->farm_id;
                if (!isset($validated['invoice_number']) || empty($validated['invoice_number'])) {
                    $validated['invoice_number'] = Sale::generateInvoiceNumber();
                }

                $sale = Sale::create($validated);

                $totalInventorySales = 0.0;
                $totalAnimalSales = 0.0;

                foreach ($validated['sales_items'] as $itemData) {
                    $sale->salesItems()->create([
                        'item_id' => $itemData['item_id'],
                        'item_type' => $itemData['item_type'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'total_price' => $itemData['total_price'],
                    ]);

                    if ($itemData['item_type'] === 'App\\Models\\InventoryItem') {
                        $totalInventorySales += (float) $itemData['total_price'];

                        $inventoryItem = InventoryItem::find($itemData['item_id']);
                        if ($inventoryItem) {
                            $requiredQty = $itemData['quantity'];

                            $inBatches = StockMovement::where('item_type', InventoryItem::class)
                                ->where('item_id', $itemData['item_id'])
                                ->where('farm_id', $sale->farm_id)
                                ->where('movement_type', 'in')
                                ->orderBy('movement_date')
                                ->orderBy('id')
                                ->lockForUpdate()
                                ->get();

                            $consumedPerBatch = [];
                            foreach ($inBatches as $batch) {
                                if ($requiredQty <= 0) {
                                    break;
                                }

                                $batchKey = $batch->batch_no ?? '___NULL_BATCH___';
                                if (!isset($consumedPerBatch[$batchKey])) {
                                    $consumedPerBatch[$batchKey] = StockMovement::where('item_type', InventoryItem::class)
                                        ->where('item_id', $itemData['item_id'])
                                        ->where('farm_id', $sale->farm_id)
                                        ->where('movement_type', 'out')
                                        ->where(function ($query) use ($batch) {
                                            if ($batch->batch_no === null) {
                                                $query->whereNull('batch_no');
                                            } else {
                                                $query->where('batch_no', $batch->batch_no);
                                            }
                                        })
                                        ->sum('quantity');
                                }

                                if ($consumedPerBatch[$batchKey] >= $batch->quantity) {
                                    $consumedPerBatch[$batchKey] -= $batch->quantity;
                                    continue;
                                }

                                $availableQty = $batch->quantity - $consumedPerBatch[$batchKey];
                                // We've accounted for all previous consumption for this batch in previous iterations
                                // or the initial sum. Now we consume what's left.
                                $consumedPerBatch[$batchKey] = 0; 


                                $consumeQty = min($requiredQty, $availableQty);

                                StockMovement::create([
                                    'farm_id' => $sale->farm_id,
                                    'item_type' => InventoryItem::class,
                                    'item_id' => $itemData['item_id'],
                                    'movement_type' => 'out',
                                    'source_type' => Sale::class,
                                    'source_event_type' => 'consumption',
                                    'source_id' => $sale->id,
                                    'quantity' => $consumeQty,
                                    'unit_cost' => $batch->unit_cost,
                                    'batch_no' => $batch->batch_no,
                                    'expiry_date' => $batch->expiry_date,
                                    'movement_date' => $sale->invoice_date,
                                    'user_id' => $sale->user_id,
                                ]);

                                $requiredQty -= $consumeQty;
                            }

                            if ($requiredQty > 0) {
                                throw new \Exception('Insufficient inventory stock for sale.');
                            }
                        }
                    } elseif ($itemData['item_type'] === 'App\\Models\\Animal') {
                        $totalAnimalSales += (float) $itemData['total_price'];

                        $animal = Animal::find($itemData['item_id']);
                        if ($animal) {
                            $animal->status = AnimalStatus::Sold->value;
                            $animal->save();
                            $requiredQty = $itemData['quantity'];

                            $inBatches = StockMovement::where('item_type', Animal::class)
                                ->where('item_id', $itemData['item_id'])
                                ->where('farm_id', $sale->farm_id)
                                ->where('movement_type', 'in')
                                ->orderBy('movement_date')
                                ->orderBy('id')
                                ->lockForUpdate()
                                ->get();

                            $consumedPerBatch = [];
                            foreach ($inBatches as $batch) {
                                if ($requiredQty <= 0) {
                                    break;
                                }

                                $batchKey = $batch->batch_no ?? '___NULL_BATCH___';
                                if (!isset($consumedPerBatch[$batchKey])) {
                                    $consumedPerBatch[$batchKey] = StockMovement::where('item_type', Animal::class)
                                        ->where('item_id', $itemData['item_id'])
                                        ->where('farm_id', $sale->farm_id)
                                        ->where('movement_type', 'out')
                                        ->where(function ($query) use ($batch) {
                                            if ($batch->batch_no === null) {
                                                $query->whereNull('batch_no');
                                            } else {
                                                $query->where('batch_no', $batch->batch_no);
                                            }
                                        })
                                        ->sum('quantity');
                                }

                                if ($consumedPerBatch[$batchKey] >= $batch->quantity) {
                                    $consumedPerBatch[$batchKey] -= $batch->quantity;
                                    continue;
                                }

                                $availableQty = $batch->quantity - $consumedPerBatch[$batchKey];
                                $consumedPerBatch[$batchKey] = 0;


                                $consumeQty = min($requiredQty, $availableQty);

                                StockMovement::create([
                                    'farm_id' => $sale->farm_id,
                                    'item_type' => Animal::class,
                                    'item_id' => $itemData['item_id'],
                                    'movement_type' => 'out',
                                    'source_type' => Sale::class,
                                    'source_event_type' => 'consumption',
                                    'source_id' => $sale->id,
                                    'quantity' => $consumeQty,
                                    'unit_cost' => $batch->unit_cost,
                                    'batch_no' => $batch->batch_no,
                                    'expiry_date' => $batch->expiry_date,
                                    'movement_date' => $sale->invoice_date,
                                    'user_id' => $sale->user_id,
                                ]);

                                $requiredQty -= $consumeQty;
                            }

                            if ($requiredQty > 0) {
                                throw new \Exception('Insufficient inventory stock for sale.');
                            }
                        }
                    }
                }

                // Auto journal integration for sales (Accounts Receivable vs Sales)
                $arAccount = ChartOfAccount::query()
                    ->where('code', '1003')
                    ->first();

                if (!$arAccount) {
                    throw new \Exception('Chart of account 1003 (Accounts Receivable) not found.');
                }

                if ($totalInventorySales > 0) {
                    $inventorySalesAccount = ChartOfAccount::query()
                        ->where('code', '4003')
                        ->first();

                    if (!$inventorySalesAccount) {
                        throw new \Exception('Chart of account 4003 (Inventory SalesItem) not found.');
                    }

                    $entry = JournalEntry::query()->create([
                        'farm_id' => $sale->farm_id,
                        'user_id' => $sale->user_id,
                        'entry_date' => $sale->invoice_date,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'description' => 'Sale invoice ' . $sale->invoice_number . ' (Inventory Items)',
                        'status' => 'posted',
                        'created_by' => $sale->user_id,
                    ]);

                    $entry->lines()->createMany([
                        [
                            'account_id' => $arAccount->id,
                            'debit_amount' => $totalInventorySales,
                            'credit_amount' => 0,
                            'narration' => 'Accounts Receivable',
                        ],
                        [
                            'account_id' => $inventorySalesAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $totalInventorySales,
                            'narration' => 'Inventory Sales',
                        ],
                    ]);
                }

                if ($totalAnimalSales > 0) {
                    $animalSalesAccount = ChartOfAccount::query()
                        ->where('code', '4002')
                        ->first();

                    if (!$animalSalesAccount) {
                        throw new \Exception('Chart of account 4002 (Animal Sales) not found.');
                    }

                    $entry = JournalEntry::query()->create([
                        'farm_id' => $sale->farm_id,
                        'user_id' => $sale->user_id,
                        'entry_date' => $sale->invoice_date,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'description' => 'Sale invoice ' . $sale->invoice_number . ' (Animal Items)',
                        'status' => 'posted',
                        'created_by' => $sale->user_id,
                    ]);

                    $entry->lines()->createMany([
                        [
                            'account_id' => $arAccount->id,
                            'debit_amount' => $totalAnimalSales,
                            'credit_amount' => 0,
                            'narration' => 'Accounts Receivable',
                        ],
                        [
                            'account_id' => $animalSalesAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $totalAnimalSales,
                            'narration' => 'Animal Sales',
                        ],
                    ]);
                }

                if ($sale->paid_amount > 0) {
                    $sale->saleTransactions()->create([
                        'sale_transaction_source_id' => $sale->id, // Polymorphic ID
                        'sale_transaction_source_type' => Sale::class, // Polymorphic Type
                        'customer_id' => $sale->customer_id,
                        'transaction_date' => $sale->invoice_date,
                        'amount' => $sale->paid_amount,
                        'payment_method' => 'Cash', // Default to Cash, can be made dynamic if needed
                        'reference_number' => SaleTransaction::generateReferenceNumber(),
                        'notes' => 'Payment received during sale creation.',
                        'farm_id' => $sale->farm_id,
                        'user_id' => $sale->user_id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function show(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['customer', 'salesItems.item', 'saleTransactions']); // Load saleTransactions
        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }

    public function printInvoice(Sale $sale)
    {
        $this->authorize('view', $sale);
        $sale->load(['customer', 'salesItems.item', 'farm', 'user']);
        return Inertia::render('Sales/PrintInvoice', ['sale' => $sale]);
    }

    public function edit(Sale $sale)
    {
        /** @var User $user */
        $user = Auth::user();
        $customers = Customer::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $inventoryItems = InventoryItem::select('id', 'name', 'unit', 'farm_id')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get()
            ->map(function ($item) {
                $item->unit_price = $item->fifo_unit_price; // Use the accessor
                return $item;
            });

        $sale->load(['salesItems.item', 'saleTransactions']); // Load saleTransactions

        $animals = Animal::select('id', 'name', 'tag', 'current_weight_kg', 'farm_id')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get()
            ->map(function ($animal) {
                $animal->unit_price = (float) StockMovement::query()
                    ->where('farm_id', $animal->farm_id)
                    ->where('item_type', Animal::class)
                    ->where('item_id', $animal->id)
                    ->where('movement_type', 'in')
                    ->latest('movement_date')
                    ->value('unit_cost') ?? 0;

                return $animal;
            });

        $saleStatuses = collect(SaleStatus::cases())->map(function ($status) {
            return ['value' => $status->value, 'name' => $status->name];
        })->toArray();

        return Inertia::render('Sales/Edit', [
            'sale' => $sale,
            'customers' => $customers,
            'inventoryItems' => $inventoryItems,
            'animals' => $animals,
            'saleStatuses' => $saleStatuses,
        ]);
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        $validated = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $saleTransactionAmount = $validated['paid_amount'];

        try {
            DB::transaction(function () use ($validated, $saleTransactionAmount, $sale, $user) {
                $validated['user_id'] = $user->id;
                $validated['farm_id'] = $user->farm_id;
                if (!isset($validated['invoice_number']) || empty($validated['invoice_number'])) {
                    $validated['invoice_number'] = Sale::generateInvoiceNumber();
                }

                $oldTotalAmount = (float) $sale->total_amount;

                $remainingAmount = $sale->total_amount - $sale->paid_amount;

                if (floatVal($saleTransactionAmount) === floatVal($sale->total_amount)) {
                    $sale->status = 'paid';
                    $sale->save();

                    throw new \Exception('Sale is already paid.');
                }

                if ($saleTransactionAmount > $remainingAmount) {
                    throw new \Exception('Paid amount cannot be greater than total amount.');
                }

                // Store original quantities for inventory items before update
                $originalSalesItems = $sale->salesItems->keyBy('id');


                $validated['paid_amount'] = (($sale->paid_amount > 0 ? $sale->paid_amount : 0) + $validated['paid_amount']);
                $sale->update($validated);

                $newTotalAmount = (float) $sale->total_amount;
                $deltaTotalAmount = $newTotalAmount - $oldTotalAmount;

                $existingSalesItemIds = $sale->salesItems->pluck('id')->toArray();
                $updatedSalesItemIds = [];

                foreach ($validated['sales_items'] as $itemData) {
                    if (isset($itemData['id'])) {
                        $salesItem = $sale->salesItems()->where('id', $itemData['id'])->first();
                        if ($salesItem) {
                            // Calculate quantity difference for existing items
                            $originalQuantity = $originalSalesItems[$salesItem->id]->quantity ?? 0;
                            $newQuantity = $itemData['quantity'];
                            $quantityDifference = $newQuantity - $originalQuantity;

                            $salesItem->update([
                                'item_id' => $itemData['item_id'],
                                'item_type' => $itemData['item_type'],
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                                'total_price' => $itemData['total_price'],
                            ]);

                            if ($itemData['item_type'] === 'App\\Models\\InventoryItem' && $quantityDifference !== 0) {
                                $inventoryItem = InventoryItem::find($itemData['item_id']);
                                if ($inventoryItem) {
                                    if ($quantityDifference > 0) { // Increased quantity, consume more
                                        $requiredQty = $quantityDifference;
                                        $inBatches = StockMovement::where('item_type', InventoryItem::class)
                                            ->where('item_id', $itemData['item_id'])
                                            ->where('farm_id', $sale->farm_id)
                                            ->where('movement_type', 'in')
                                            ->orderBy('movement_date') // FIFO
                                            ->lockForUpdate()
                                            ->get();

                                        foreach ($inBatches as $batch) {
                                            if ($requiredQty <= 0) break;

                                            $alreadyConsumed = StockMovement::where('item_type', InventoryItem::class)
                                                ->where('item_id', $itemData['item_id'])
                                                ->where('farm_id', $sale->farm_id)
                                                ->where('movement_type', 'out')
                                                ->where('batch_no', $batch->batch_no)
                                                ->sum('quantity');

                                            $availableQty = $batch->quantity - $alreadyConsumed;
                                            if ($availableQty <= 0) continue;

                                            $consumeQty = min($requiredQty, $availableQty);

                                            StockMovement::create([
                                                'farm_id' => $sale->farm_id,
                                                'item_type' => InventoryItem::class,
                                                'item_id' => $itemData['item_id'],
                                                'movement_type' => 'out',
                                                'source_type' => Sale::class,
                                                'source_event_type' => 'consumption',
                                                'source_id' => $sale->id,
                                                'quantity' => $consumeQty,
                                                'unit_cost' => $batch->unit_cost,
                                                'batch_no' => $batch->batch_no,
                                                'expiry_date' => $batch->expiry_date,
                                                'movement_date' => $sale->invoice_date,
                                                'user_id' => $sale->user_id,
                                            ]);
                                            $requiredQty -= $consumeQty;
                                        }
                                        if ($requiredQty > 0) {
                                            throw new \Exception('Insufficient inventory stock for sale update.');
                                        }
                                    } elseif ($quantityDifference < 0) { // Decreased quantity, return stock
                                        $returnQty = abs($quantityDifference);
                                        $outs = StockMovement::where('movement_type', 'out')
                                            ->where('item_type', InventoryItem::class)
                                            ->where('item_id', $itemData['item_id'])
                                            ->where('source_type', Sale::class)
                                            ->where('source_id', $sale->id)
                                            ->orderByDesc('movement_date')
                                            ->lockForUpdate()
                                            ->get();

                                        foreach ($outs as $out) {
                                            if ($returnQty <= 0) break;

                                            $rollbackQty = min($returnQty, $out->quantity);
                                            if ($rollbackQty === $out->quantity) {
                                                $out->delete();
                                            } else {
                                                $out->decrement('quantity', $rollbackQty);
                                            }
                                            $returnQty -= $rollbackQty;
                                        }
                                    }
                                }
                            }
                        }
                        $updatedSalesItemIds[] = $itemData['id'];
                    } else {
                        // New item added to sale
                        $saleItem = $sale->salesItems()->create([
                            'item_id' => $itemData['item_id'],
                            'item_type' => $itemData['item_type'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'total_price' => $itemData['total_price'],
                        ]);

                        if ($itemData['item_type'] === 'App\\Models\\InventoryItem') {
                            $inventoryItem = InventoryItem::find($itemData['item_id']);
                            if ($inventoryItem) {
                                $requiredQty = $itemData['quantity'];

                                $inBatches = StockMovement::where('item_type', InventoryItem::class)
                                    ->where('item_id', $itemData['item_id'])
                                    ->where('farm_id', $sale->farm_id)
                                    ->where('movement_type', 'in')
                                    ->orderBy('movement_date') // FIFO
                                    ->lockForUpdate()
                                    ->get();

                                foreach ($inBatches as $batch) {
                                    if ($requiredQty <= 0) break;

                                    $alreadyConsumed = StockMovement::where('item_type', InventoryItem::class)
                                        ->where('item_id', $itemData['item_id'])
                                        ->where('farm_id', $sale->farm_id)
                                        ->where('movement_type', 'out')
                                        ->where('batch_no', $batch->batch_no)
                                        ->sum('quantity');

                                    $availableQty = $batch->quantity - $alreadyConsumed;
                                    if ($availableQty <= 0) continue;

                                    $consumeQty = min($requiredQty, $availableQty);

                                    StockMovement::create([
                                        'farm_id' => $sale->farm_id,
                                        'item_type' => InventoryItem::class,
                                        'item_id' => $itemData['item_id'],
                                        'movement_type' => 'out',
                                        'source_type' => Sale::class,
                                        'source_event_type' => 'consumption',
                                        'source_id' => $sale->id,
                                        'quantity' => $consumeQty,
                                        'unit_cost' => $batch->unit_cost,
                                        'batch_no' => $batch->batch_no,
                                        'expiry_date' => $batch->expiry_date,
                                        'movement_date' => $sale->invoice_date,
                                        'user_id' => $sale->user_id,
                                    ]);
                                    $requiredQty -= $consumeQty;
                                }
                                if ($requiredQty > 0) {
                                    throw new \Exception('Insufficient inventory stock for new item in sale.');
                                }
                            }
                        }
                    }
                }

                // Adjust journals based on total amount increment/decrement
                if ($deltaTotalAmount != 0.0) {
                    $arAccount = ChartOfAccount::query()->where('code', '1003')->first();
                    if (!$arAccount) {
                        throw new \Exception('Chart of account 1003 (Accounts Receivable) not found.');
                    }

                    $inventorySalesAccount = ChartOfAccount::query()->where('code', '4003')->first();
                    $animalSalesAccount = ChartOfAccount::query()->where('code', '4002')->first();

                    // Determine which sales type(s) exist in the updated sale
                    $hasInventory = collect($validated['sales_items'])->contains(fn($i) => ($i['item_type'] ?? null) === 'App\\Models\\InventoryItem');
                    $hasAnimal = collect($validated['sales_items'])->contains(fn($i) => ($i['item_type'] ?? null) === 'App\\Models\\Animal');

                    // If both types exist, allocate delta proportionally by current totals
                    $currentInventoryTotal = $sale->salesItems()->where('item_type', 'App\\Models\\InventoryItem')->sum('total_price');
                    $currentAnimalTotal = $sale->salesItems()->where('item_type', 'App\\Models\\Animal')->sum('total_price');
                    $currentTotal = (float) $currentInventoryTotal + (float) $currentAnimalTotal;

                    $allocInventory = 0.0;
                    $allocAnimal = 0.0;

                    if ($hasInventory && $hasAnimal && $currentTotal > 0) {
                        $allocInventory = $deltaTotalAmount * ((float) $currentInventoryTotal / $currentTotal);
                        $allocAnimal = $deltaTotalAmount * ((float) $currentAnimalTotal / $currentTotal);
                    } elseif ($hasInventory) {
                        $allocInventory = $deltaTotalAmount;
                    } elseif ($hasAnimal) {
                        $allocAnimal = $deltaTotalAmount;
                    }

                    $createAdjustmentEntry = function (string $label, ChartOfAccount $salesAccount, float $amount) use ($sale, $arAccount) {
                        if ($amount == 0.0) {
                            return;
                        }

                        $abs = abs($amount);

                        $entry = JournalEntry::query()->create([
                            'farm_id' => $sale->farm_id,
                            'user_id' => $sale->user_id,
                            'entry_date' => $sale->invoice_date,
                            'reference_type' => 'sale',
                            'reference_id' => $sale->id,
                            'description' => 'Sale adjustment ' . $sale->invoice_number . ' (' . $label . ')',
                            'status' => 'posted',
                            'created_by' => $sale->user_id,
                        ]);

                        if ($amount > 0) {
                            // Increase: DR AR, CR Sales
                            $entry->lines()->createMany([
                                [
                                    'account_id' => $arAccount->id,
                                    'debit_amount' => $abs,
                                    'credit_amount' => 0,
                                    'narration' => 'Accounts Receivable (Adjustment)',
                                ],
                                [
                                    'account_id' => $salesAccount->id,
                                    'debit_amount' => 0,
                                    'credit_amount' => $abs,
                                    'narration' => $label . ' (Adjustment)',
                                ],
                            ]);
                        } else {
                            // Decrease: DR Sales, CR AR
                            $entry->lines()->createMany([
                                [
                                    'account_id' => $salesAccount->id,
                                    'debit_amount' => $abs,
                                    'credit_amount' => 0,
                                    'narration' => $label . ' (Adjustment)',
                                ],
                                [
                                    'account_id' => $arAccount->id,
                                    'debit_amount' => 0,
                                    'credit_amount' => $abs,
                                    'narration' => 'Accounts Receivable (Adjustment)',
                                ],
                            ]);
                        }
                    };

                    if ($allocInventory != 0.0) {
                        if (!$inventorySalesAccount) {
                            throw new \Exception('Chart of account 4003 (Inventory SalesItem) not found.');
                        }
                        $createAdjustmentEntry('Inventory Items', $inventorySalesAccount, $allocInventory);
                    }

                    if ($allocAnimal != 0.0) {
                        if (!$animalSalesAccount) {
                            throw new \Exception('Chart of account 4002 (Animal Sales) not found.');
                        }
                        $createAdjustmentEntry('Animal Items', $animalSalesAccount, $allocAnimal);
                    }
                }

                // Handle deleted sales items
                $itemsToDelete = array_diff($existingSalesItemIds, $updatedSalesItemIds);
                foreach ($itemsToDelete as $deletedItemId) {
                    $deletedSalesItem = $originalSalesItems[$deletedItemId];
                    if ($deletedSalesItem->item_type === 'App\\Models\\InventoryItem') {
                        $inventoryItem = InventoryItem::find($deletedSalesItem->item_id);
                        if ($inventoryItem) {
                            $returnQty = $deletedSalesItem->quantity;
                            $outs = StockMovement::where('movement_type', 'out')
                                ->where('item_type', InventoryItem::class)
                                ->where('item_id', $deletedSalesItem->item_id)
                                ->where('source_type', Sale::class)
                                ->where('source_id', $sale->id)
                                ->orderByDesc('movement_date')
                                ->lockForUpdate()
                                ->get();

                            foreach ($outs as $out) {
                                if ($returnQty <= 0) break;

                                $rollbackQty = min($returnQty, $out->quantity);
                                if ($rollbackQty === $out->quantity) {
                                    $out->delete();
                                } else {
                                    $out->decrement('quantity', $rollbackQty);
                                }
                                $returnQty -= $rollbackQty;
                            }
                        }
                    }
                    $sale->salesItems()->where('id', $deletedItemId)->delete();
                }


                if ($saleTransactionAmount > 0) {
                    $sale->saleTransactions()->create([
                        'sale_transaction_source_id' => $sale->id, // Polymorphic ID
                        'sale_transaction_source_type' => Sale::class, // Polymorphic Type
                        'customer_id' => $sale->customer_id,
                        'transaction_date' => $validated['invoice_date'],
                        'amount' => $saleTransactionAmount,
                        'payment_method' => 'Cash', // Default to Cash, can be made dynamic if needed
                        'reference_number' => SaleTransaction::generateReferenceNumber(),
                        'notes' => 'Payment received during sale creation.',
                        'farm_id' => $sale->farm_id,
                        'user_id' => $sale->user_id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }


        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Revert stock movements for all sales items
            foreach ($sale->salesItems as $salesItem) {
                if ($salesItem->item_type === 'App\\Models\\InventoryItem') {
                    $inventoryItem = InventoryItem::find($salesItem->item_id);
                    if ($inventoryItem) {
                        $returnQty = $salesItem->quantity;
                        $outs = StockMovement::where('movement_type', 'out')
                            ->where('item_type', InventoryItem::class)
                            ->where('item_id', $salesItem->item_id)
                            ->where('source_type', Sale::class)
                            ->where('source_id', $sale->id)
                            ->orderByDesc('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($outs as $out) {
                            if ($returnQty <= 0) break;

                            $rollbackQty = min($returnQty, $out->quantity);
                            if ($rollbackQty === $out->quantity) {
                                $out->delete();
                            } else {
                                $out->decrement('quantity', $rollbackQty);
                            }
                            $returnQty -= $rollbackQty;
                        }
                    }
                } elseif ($salesItem->item_type === 'App\\Models\\Animal') {
                    $animal = Animal::find($salesItem->item_id);
                    if ($animal) {
                        // Mark animal back as active (undo sold)
                        $animal->status = AnimalStatus::Active->value;
                        $animal->save();

                        // Revert animal stock movements created by this sale
                        $returnQty = $salesItem->quantity;
                        $outs = StockMovement::where('movement_type', 'out')
                            ->where('item_type', Animal::class)
                            ->where('item_id', $salesItem->item_id)
                            ->where('source_type', Sale::class)
                            ->where('source_id', $sale->id)
                            ->orderByDesc('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($outs as $out) {
                            if ($returnQty <= 0) break;

                            $rollbackQty = min($returnQty, $out->quantity);
                            if ($rollbackQty === $out->quantity) {
                                $out->delete();
                            } else {
                                $out->decrement('quantity', $rollbackQty);
                            }
                            $returnQty -= $rollbackQty;
                        }
                    }
                }

                $salesItem->delete();
            }

            // Delete any associated sale transactions
            $sale->saleTransactions()->delete(); // Delete polymorphic transactions

            // Delete the sale itself
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
