<?php

namespace App\Http\Controllers;

use App\Models\FeedingRecord;
use App\Models\Animal;
use App\Models\FeedingItem; // Import FeedingItem model
use App\Models\InventoryItem; // Import InventoryItem model
use App\Models\Herd; // Import Herd model
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Requests\StoreFeedingRecordRequest; // Import StoreFeedingRecordRequest
use App\Http\Requests\UpdateFeedingRecordRequest; // Import UpdateFeedingRecordRequest
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;

class FeedingRecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FeedingRecord::class, 'feeding');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $feedings = FeedingRecord::with(['animal', 'group', 'feedingItems.item'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Feedings/Index', [
            'feedings' => $feedings,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $herds = Herd::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $inventoryItems = InventoryItem::select('inventory_items.id', 'inventory_items.name', 'inventory_items.quantity', 'inventory_items.unit')
            ->join('inventory_categories', 'inventory_categories.id', '=', 'inventory_items.inventory_category_id')
            ->where('inventory_categories.name', 'Animal Feed')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('inventory_items.farm_id', $user->farm_id);
            })
            ->get();

        return Inertia::render('Feedings/Create', [
            'animals' => $animals,
            'herds' => $herds,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function store(StoreFeedingRecordRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Enforce farm_id for farm owners
            if ($user->hasRole('farm owner')) {
                if (!empty($validated['animal_id'])) {
                    $animal = Animal::find($validated['animal_id']);
                    if ($animal && $animal->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
                    }
                }
                if (!empty($validated['group_id'])) {
                    $herd = Herd::find($validated['group_id']);
                    if ($herd && $herd->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['group_id' => 'The selected group does not belong to your farm.'])->withInput();
                    }
                }
                $validated['farm_id'] = $user->farm_id;
                $validated['user_id'] = $user->id;
            } else {
                // For non-farm-owner users (e.g. staff with permissions), infer farm_id from the selected animal/group.
                // This prevents "Undefined array key farm_id" and keeps records farm-scoped.
                $farmId = null;

                if (!empty($validated['animal_id'])) {
                    $farmId = Animal::whereKey($validated['animal_id'])->value('farm_id');
                } elseif (!empty($validated['group_id'])) {
                    $farmId = Herd::whereKey($validated['group_id'])->value('farm_id');
                }

                if (!$farmId) {
                    DB::rollBack();
                    return back()->withErrors(['animal_id' => 'Unable to determine farm for this feeding record.'])->withInput();
                }

                $validated['farm_id'] = $farmId;
                $validated['user_id'] = $user->id;
            }

            $feedingRecord = FeedingRecord::create([
                'farm_id' => $validated['farm_id'],
                'user_id' => $validated['user_id'],
                'animal_id' => $validated['animal_id'] ?? null,
                'group_id' => $validated['group_id'] ?? null,
                'feeding_date' => $validated['feeding_date'],
                'feeding_time' => $validated['feeding_time'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $totalFeedCost = 0.0;

            foreach ($validated['feeding_items'] as $itemData) {
                $feedingItem = $feedingRecord->feedingItems()->create([
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                ]);

                // Record stock movement for consumption (FIFO)
                $requiredQty = $itemData['quantity'];

                $inBatches = StockMovement::where('item_type', InventoryItem::class)
                    ->where('item_id', $itemData['item_id'])
                    ->where('farm_id', $validated['farm_id'])
                    ->where('movement_type', 'in')
                    ->orderBy('movement_date') // FIFO
                    ->lockForUpdate()
                    ->get();

                foreach ($inBatches as $batch) {
                    if ($requiredQty <= 0) {
                        break;
                    }

                    $alreadyConsumed = StockMovement::where('item_type', InventoryItem::class)
                        ->where('item_id', $itemData['item_id'])
                        ->where('farm_id', $validated['farm_id'])
                        ->where('movement_type', 'out')
                        ->where('batch_no', $batch->batch_no)
                        ->sum('quantity');

                    $availableQty = $batch->quantity - $alreadyConsumed;

                    if ($availableQty <= 0) {
                        continue;
                    }

                    $consumeQty = min($requiredQty, $availableQty);

                    StockMovement::create([
                        'farm_id' => $validated['farm_id'],
                        'item_type' => InventoryItem::class,
                        'item_id' => $itemData['item_id'],
                        'movement_type' => 'out',
                        'source_type' => FeedingRecord::class,
                        'source_event_type' => 'consumption',
                        'source_id' => $feedingRecord->id,
                        'quantity' => $consumeQty,
                        'unit_cost' => $batch->unit_cost,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => $batch->expiry_date,
                        'movement_date' => $validated['feeding_date'],
                        'user_id' => $user->id,
                    ]);

                    $totalFeedCost += ((float) $consumeQty) * ((float) $batch->unit_cost);

                    $requiredQty -= $consumeQty;
                }

                if ($requiredQty > 0) {
                    throw new \Exception('Insufficient inventory stock for feeding.');
                }
            }

            // Auto journal integration for feeding consumption:
            // DR 5001 - Feed Expense
            // CR 1005 - Feed Inventory
            if ($totalFeedCost > 0) {
                $feedExpenseAccount = ChartOfAccount::query()->where('code', '5001')->first();
                if (!$feedExpenseAccount) {
                    throw new \Exception('Chart of account 5001 (Feed Expense) not found.');
                }

                $feedInventoryAccount = ChartOfAccount::query()->where('code', '1005')->first();
                if (!$feedInventoryAccount) {
                    throw new \Exception('Chart of account 1005 (Feed Inventory) not found.');
                }

                $entry = JournalEntry::query()->create([
                    'farm_id' => $feedingRecord->farm_id,
                    'user_id' => $feedingRecord->user_id,
                    'entry_date' => $feedingRecord->feeding_date,
                    'reference_type' => 'feeding',
                    'reference_id' => $feedingRecord->id,
                    'description' => 'Feed consumption (Feeding #' . $feedingRecord->id . ')',
                    'status' => 'posted',
                    'created_by' => $feedingRecord->user_id,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_id' => $feedExpenseAccount->id,
                        'debit_amount' => $totalFeedCost,
                        'credit_amount' => 0,
                        'narration' => 'Feed Expense',
                    ],
                    [
                        'account_id' => $feedInventoryAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $totalFeedCost,
                        'narration' => 'Feed Inventory',
                    ],
                ]);
            }

            DB::commit();
            return redirect()->route('feedings.index')->with('success', 'Feeding recorded.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record feeding: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(FeedingRecord $feeding)
    {
        $feeding->load(['animal', 'group', 'feedingItems.item']);
        return Inertia::render('Feedings/Show', ['feeding' => $feeding]);
    }

    public function edit(FeedingRecord $feeding)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $feeding->load(['animal', 'group', 'feedingItems.item']);

        // Format the feeding record for the frontend
        $formattedFeeding = $feeding->toArray();

        // Ensure feeding_date is in the correct format for date input
        if ($feeding->feeding_date) {
            $formattedFeeding['feeding_date'] = $feeding->feeding_date->format('Y-m-d');
        }

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $herds = Herd::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $inventoryItems = InventoryItem::select('inventory_items.id', 'inventory_items.name', 'inventory_items.quantity', 'inventory_items.unit')
            ->join('inventory_categories', 'inventory_categories.id', '=', 'inventory_items.inventory_category_id')
            ->where('inventory_categories.name', 'Animal Feed')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('inventory_items.farm_id', $user->farm_id);
            })
            ->get();

        return Inertia::render('Feedings/Edit', [
            'feeding' => $formattedFeeding,
            'animals' => $animals,
            'herds' => $herds,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function update(UpdateFeedingRecordRequest $request, FeedingRecord $feeding)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validated();

            // Enforce farm_id for farm owners
            if ($user->hasRole('farm owner')) {
                if ($validated['animal_id']) {
                    $animal = Animal::find($validated['animal_id']);
                    if ($animal && $animal->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
                    }
                }
                if ($validated['group_id']) {
                    $herd = Herd::find($validated['group_id']);
                    if ($herd && $herd->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['group_id' => 'The selected group does not belong to your farm.'])->withInput();
                    }
                }
                $validated['farm_id'] = $user->farm_id;
            }

            $feeding->update([
                'animal_id' => $validated['animal_id'],
                'group_id' => $validated['group_id'],
                'feeding_date' => $validated['feeding_date'],
                'feeding_time' => $validated['feeding_time'],
                'notes' => $validated['notes'],
            ]);

            $existingItems = $feeding->feedingItems->keyBy('id');
            $updatedItemIds = [];

            $totalFeedCostDelta = 0.0;

            foreach ($validated['feeding_items'] as $itemData) {
                $oldQuantity = 0;
                $feedingItem = null;

                if (!empty($itemData['id'])) {
                    $feedingItem = $existingItems[$itemData['id']];
                    $oldQuantity = $feedingItem->quantity;
                }

                $newQuantity = $itemData['quantity'];
                $delta = $newQuantity - $oldQuantity;

                $feedingItem = $feeding->feedingItems()->updateOrCreate(
                    ['id' => $itemData['id'] ?? null],
                    [
                        'item_id' => $itemData['item_id'],
                        'quantity' => $newQuantity,
                    ]
                );

                if ($delta > 0) {
                    $requiredQty = $delta;

                    $inBatches = StockMovement::where('item_type', InventoryItem::class)
                        ->where('item_id', $itemData['item_id'])
                        ->where('farm_id', $user->farm_id)
                        ->where('movement_type', 'in')
                        ->orderBy('movement_date') // FIFO
                        ->lockForUpdate()
                        ->get();

                    foreach ($inBatches as $batch) {
                        if ($requiredQty <= 0) {
                            break;
                        }

                        $alreadyConsumed = StockMovement::where('item_type', InventoryItem::class)
                            ->where('item_id', $itemData['item_id'])
                            ->where('farm_id', $user->farm_id)
                            ->where('movement_type', 'out')
                            ->where('batch_no', $batch->batch_no)
                            ->sum('quantity');

                        $availableQty = $batch->quantity - $alreadyConsumed;

                        if ($availableQty <= 0) {
                            continue;
                        }

                        $consumeQty = min($requiredQty, $availableQty);

                        StockMovement::create([
                            'farm_id' => $user->farm_id,
                            'item_type' => InventoryItem::class,
                            'item_id' => $itemData['item_id'],
                            'movement_type' => 'out',
                            'source_type' => FeedingRecord::class,
                            'source_event_type' => 'consumption',
                            'source_id' => $feeding->id,
                            'quantity' => $consumeQty,
                            'unit_cost' => $batch->unit_cost,
                            'batch_no' => $batch->batch_no,
                            'expiry_date' => $batch->expiry_date,
                            'movement_date' => $validated['feeding_date'],
                            'user_id' => $user->id,
                        ]);

                        $totalFeedCostDelta += ((float) $consumeQty) * ((float) $batch->unit_cost);

                        $requiredQty -= $consumeQty;
                    }

                    if ($requiredQty > 0) {
                        throw new \Exception('Insufficient inventory stock for feeding.');
                    }
                }

                if ($delta < 0) {
                    $returnQty = abs($delta);

                    $outs = StockMovement::where('movement_type', 'out')
                        ->where('item_type', InventoryItem::class)
                        ->where('item_id', $itemData['item_id'])
                        ->where('source_type', FeedingRecord::class)
                        ->where('source_id', $feeding->id)
                        ->orderByDesc('movement_date')
                        ->lockForUpdate()
                        ->get();

                    foreach ($outs as $out) {
                        if ($returnQty <= 0) {
                            break;
                        }

                        $rollbackQty = min($returnQty, $out->quantity);

                        $totalFeedCostDelta -= ((float) $rollbackQty) * ((float) $out->unit_cost);

                        if ($rollbackQty === $out->quantity) {
                            $out->delete();
                        } else {
                            $out->decrement('quantity', $rollbackQty);
                        }

                        $returnQty -= $rollbackQty;
                    }
                }
                $updatedItemIds[] = $feedingItem->id;
            }

            $itemsToDelete = array_diff($existingItems->pluck('id')->toArray(), $updatedItemIds);
            if (!empty($itemsToDelete)) {
                foreach ($itemsToDelete as $itemId) {
                    $feedingItem = $existingItems[$itemId];
                    if ($feedingItem && $feedingItem->quantity > 0) {
                        $returnQty = $feedingItem->quantity;

                        $outs = StockMovement::where('movement_type', 'out')
                            ->where('item_type', InventoryItem::class)
                            ->where('item_id', $feedingItem->item_id)
                            ->where('source_type', FeedingRecord::class)
                            ->where('source_id', $feeding->id)
                            ->orderByDesc('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($outs as $out) {
                            if ($returnQty <= 0) {
                                break;
                            }

                            $rollbackQty = min($returnQty, $out->quantity);

                            $totalFeedCostDelta -= ((float) $rollbackQty) * ((float) $out->unit_cost);

                            if ($rollbackQty === $out->quantity) {
                                $out->delete();
                            } else {
                                $out->decrement('quantity', $rollbackQty);
                            }

                            $returnQty -= $rollbackQty;
                        }
                    }
                    $feedingItem->delete();
                }
            }

            // Post adjustment journal if feed consumption cost changed
            if ($totalFeedCostDelta != 0.0) {
                $feedExpenseAccount = ChartOfAccount::query()->where('code', '5001')->first();
                if (!$feedExpenseAccount) {
                    throw new \Exception('Chart of account 5001 (Feed Expense) not found.');
                }

                $feedInventoryAccount = ChartOfAccount::query()->where('code', '1005')->first();
                if (!$feedInventoryAccount) {
                    throw new \Exception('Chart of account 1005 (Feed Inventory) not found.');
                }

                $abs = abs($totalFeedCostDelta);

                $entry = JournalEntry::query()->create([
                    'farm_id' => $feeding->farm_id,
                    'user_id' => $user->id,
                    'entry_date' => $feeding->feeding_date,
                    'reference_type' => 'feeding',
                    'reference_id' => $feeding->id,
                    'description' => 'Feed consumption adjustment (Feeding #' . $feeding->id . ')',
                    'status' => 'posted',
                    'created_by' => $user->id,
                ]);

                if ($totalFeedCostDelta > 0) {
                    // Increased consumption: DR Expense, CR Inventory
                    $entry->lines()->createMany([
                        [
                            'account_id' => $feedExpenseAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Feed Expense (Adjustment)',
                        ],
                        [
                            'account_id' => $feedInventoryAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Feed Inventory (Adjustment)',
                        ],
                    ]);
                } else {
                    // Decreased consumption: DR Inventory, CR Expense
                    $entry->lines()->createMany([
                        [
                            'account_id' => $feedInventoryAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Feed Inventory (Adjustment)',
                        ],
                        [
                            'account_id' => $feedExpenseAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Feed Expense (Adjustment)',
                        ],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('feedings.index')->with('success', 'Feeding record updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update feeding record: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(FeedingRecord $feeding)
    {
        DB::beginTransaction();
        try {
            foreach ($feeding->feedingItems as $feedingItem) {
                // Revert stock movement for each feeding item
                $returnQty = $feedingItem->quantity;

                $outs = StockMovement::where('movement_type', 'out')
                    ->where('item_type', InventoryItem::class)
                    ->where('item_id', $feedingItem->item_id)
                    ->where('source_type', FeedingRecord::class)
                    ->where('source_id', $feeding->id)
                    ->orderByDesc('movement_date')
                    ->lockForUpdate()
                    ->get();

                foreach ($outs as $out) {
                    if ($returnQty <= 0) {
                        break;
                    }

                    $rollbackQty = min($returnQty, $out->quantity);

                    if ($rollbackQty === $out->quantity) {
                        $out->delete();
                    } else {
                        $out->decrement('quantity', $rollbackQty);
                    }

                    $returnQty -= $rollbackQty;
                }
                $feedingItem->delete(); // Delete the child record
            }

            $feeding->delete(); // Delete the master record
            DB::commit();
            return redirect()->route('feedings.index')->with('success', 'Feeding record deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete feeding record: ' . $e->getMessage()])->withInput();
        }
    }
}
