<?php

namespace App\Http\Controllers;

use App\Models\DiseaseTreatment;
use App\Models\HealthIssue;
use App\Models\StaffProfile;
use App\Models\Treatment; // Import Treatment model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use App\Models\HealthEvent; // Import HealthEvent model
use App\Models\User; // Import User model
use Inertia\Inertia;
use App\Models\Medicine; // Import Medicine model
use App\Models\StockMovement;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB; // Import DB facade

class DiseaseTreatmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DiseaseTreatment::class, 'disease_treatment');
    }

    public function getTreatmentMedications(Treatment $treatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Ensure the treatment belongs to the user's farm
        if ($user->hasRole('farm owner') && $treatment->farm_id !== $user->farm_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $medications = $treatment->medicines()
            ->withPivot(['dose', 'frequency', 'duration_days'])
            ->get();

        // Attach current unit_cost from stock_movements (latest IN batch by movement_date)
        $unitCosts = StockMovement::query()
            ->select('item_id', DB::raw('MAX(movement_date) as latest_movement_date'))
            ->where('farm_id', $user->farm_id)
            ->where('item_type', Medicine::class)
            ->where('movement_type', 'in')
            ->whereIn('item_id', $medications->pluck('id'))
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        $latestCosts = StockMovement::query()
            ->select('item_id', 'unit_cost', 'movement_date')
            ->where('farm_id', $user->farm_id)
            ->where('item_type', Medicine::class)
            ->where('movement_type', 'in')
            ->whereIn('item_id', $medications->pluck('id'))
            ->get()
            ->groupBy('item_id')
            ->map(function ($rows) use ($unitCosts) {
                $itemId = $rows->first()->item_id;
                $latestDate = optional($unitCosts->get($itemId))->latest_movement_date;

                $latestRow = $rows
                    ->where('movement_date', $latestDate)
                    ->sortByDesc('id')
                    ->first();

                return $latestRow?->unit_cost;
            });

        $medications->transform(function ($med) use ($latestCosts) {
            $med->unit_cost = $latestCosts->get($med->id);
            return $med;
        });

        return response()->json($medications);
    }

    // The missing closing brace was here, before the next method definition.

    public function getHealthEventsByHealthIssue(HealthIssue $healthIssue)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $healthEvents = HealthEvent::where('health_issue_id', $healthIssue->id)
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereNull('resolved_at') // Only show health events that are not resolved
            ->select('id', 'title', 'occurred_at') // Select relevant fields
            ->get();

        return response()->json($healthEvents);
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $treatments = DiseaseTreatment::with(['healthIssue.animal', 'administeredBy', 'treatment', 'diseaseTreatmentMedications.medicine'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->whereHas('healthIssue.animal', function ($q) use ($user) {
                    $q->where('farm_id', $user->farm_id);
                });
            })
            ->when($q, fn($qb) => $qb->whereHas('healthIssue', fn($hq) => $hq->where('disease_name', 'like', "%$q%"))
                ->orWhere('treatment', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = DiseaseTreatment::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->whereHas('healthIssue.animal', function ($q) use ($user) {
                $q->where('farm_id', $user->farm_id);
            });
        });

        $statusCounts = [
            'planned' => (clone $baseQuery)->where('status', 'planned')->count(),
            'ongoing' => (clone $baseQuery)->where('status', 'ongoing')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'discontinued' => (clone $baseQuery)->where('status', 'discontinued')->count(),
        ];

        // The `disease_treatments` table no longer has a `cost` column.
        // Total treatment cost is stored on associated medication records.  We
        // aggregate `total_cost` from the join table so that the same query
        // scoping rules (farm owner filtering) still apply.
        $totalCost = (clone $baseQuery)
            ->join('disease_treatment_medications', 'disease_treatments.id', '=', 'disease_treatment_medications.disease_treatment_id')
            ->sum('disease_treatment_medications.total_cost');

        return Inertia::render('DiseaseTreatments/Index', [
            'treatments' => $treatments,
            'filters' => $request->only('q'),
            'statusCounts' => $statusCounts,
            'totalCost' => $totalCost,
        ]);
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $healthIssues = HealthIssue::with('animal')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $treatments = Treatment::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        // Pass an empty array for healthEvents initially, it will be populated via AJAX
        return Inertia::render('DiseaseTreatments/Create', [
            'healthIssues' => $healthIssues,
            'staff' => $staff,
            'treatments' => $treatments,
            'healthEvents' => [],
            'allStaff' => $staff, // Pass all staff for medication line items
            'resolved_at' => null, // Initialize resolved_at for the create form
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'health_issue_id' => 'required|exists:health_issues,id',
            'health_event_id' => 'nullable|exists:health_events,id',
            'treatment_id' => 'required|exists:treatments,id',
            'description' => 'nullable|string',
            'resolved_at' => 'nullable|date',
            'status' => 'required|in:planned,ongoing,completed,discontinued',
            'notes' => 'nullable|string',
            'disease_treatment_medications' => 'nullable|array',
            'disease_treatment_medications.*.medicine_id' => 'required|exists:medicines,id',
            'disease_treatment_medications.*.dose' => 'nullable|string|max:100',
            'disease_treatment_medications.*.frequency' => 'nullable|string|max:100',
            'disease_treatment_medications.*.duration_days' => 'nullable|integer|min:0',
            'disease_treatment_medications.*.status' => 'required|in:planned,ongoing,completed,discontinued',
            'disease_treatment_medications.*.started_at' => 'nullable|date',
            'disease_treatment_medications.*.ended_at' => 'nullable|date|after_or_equal:disease_treatment_medications.*.started_at',
            'disease_treatment_medications.*.qty' => 'nullable|integer|min:0',
            'disease_treatment_medications.*.unit_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'disease_treatment_medications.*.total_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'disease_treatment_medications.*.notes' => 'nullable|string',
        ]);

        // Ensure health issue belongs to farm
        if ($user->hasRole('farm owner')) {
            HealthIssue::where('id', $validated['health_issue_id'])
                ->where('farm_id', $user->farm_id)
                ->firstOrFail();
        }

        try {

            DB::transaction(function () use ($validated, $user) {

                /* -----------------------------
         | 1️⃣ Create Disease Treatment
         ----------------------------- */
                $treatment = DiseaseTreatment::create([
                    'health_issue_id' => $validated['health_issue_id'],
                    'health_event_id' => $validated['health_event_id'] ?? null,
                    'treatment_id' => $validated['treatment_id'],
                    'farm_id' => $user->farm_id,
                    'user_id' => $user->id,
                    'description' => $validated['description'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => $validated['status'],
                ]);

                /* ----------------------------------
         | 2️⃣ Handle Medicine Consumption
         ---------------------------------- */
                foreach ($validated['disease_treatment_medications'] ?? [] as $med) {

                    $qty = (float) ($med['qty'] ?? 0);
                    $unitCost = isset($med['unit_cost']) ? (float) $med['unit_cost'] : null;
                    $totalCost = isset($med['total_cost']) ? (float) $med['total_cost'] : null;

                    if ($unitCost !== null && $totalCost === null) {
                        $totalCost = $qty * $unitCost;
                    }

                    // Save medication usage record
                    $treatment->diseaseTreatmentMedications()->create([
                        'medicine_id' => $med['medicine_id'],
                        'farm_id' => $user->farm_id,
                        'user_id' => $user->id,
                        'qty' => $med['qty'],
                        'unit_cost' => $unitCost,
                        'total_cost' => $totalCost,
                        'dose' => $med['dose'] ?? null,
                        'frequency' => $med['frequency'] ?? null,
                        'duration_days' => $med['duration_days'] ?? null,
                        'status' => $med['status'] ?? null,
                        'started_at' => $med['started_at'] ?? now(),
                        'ended_at' => $med['ended_at'] ?? null,
                        'notes' => $med['notes'] ?? null,
                    ]);

                    $requiredQty = $med['qty'];

                    /* ------------------------------------------------
             | 3️⃣ Fetch IN batches (FEFO: expiry first)
             ------------------------------------------------ */
                    $inBatches = StockMovement::where('item_type', Medicine::class)
                        ->where('item_id', $med['medicine_id'])
                        ->where('farm_id', $user->farm_id)
                        ->where('movement_type', 'in')
                        ->whereDate('expiry_date', '>=', now())
                        ->orderBy('expiry_date')
                        ->orderBy('movement_date')
                        ->lockForUpdate()
                        ->get();

                    foreach ($inBatches as $batch) {

                        if ($requiredQty <= 0) {
                            break;
                        }

                        // Calculate consumed quantity for this batch
                        $alreadyConsumed = StockMovement::where('item_type', Medicine::class)
                            ->where('item_id', $med['medicine_id'])
                            ->where('farm_id', $user->farm_id)
                            ->where('movement_type', 'out')
                            ->where('batch_no', $batch->batch_no)
                            ->sum('quantity');

                        $availableQty = $batch->quantity - $alreadyConsumed;

                        if ($availableQty <= 0) {
                            continue;
                        }

                        $consumeQty = min($requiredQty, $availableQty);

                        // Create OUT stock movement (inherits cost + batch)
                        StockMovement::create([
                            'farm_id' => $user->farm_id,
                            'item_type' => Medicine::class,
                            'item_id' => $med['medicine_id'],
                            'movement_type' => 'out',
                            'source_type' => DiseaseTreatment::class,
                            'source_event_type' => 'consumption',
                            'source_id' => $treatment->id,
                            'quantity' => $consumeQty,
                            'unit_cost' => $batch->unit_cost, // ✅ inherited
                            'batch_no' => $batch->batch_no,
                            'expiry_date' => $batch->expiry_date,
                            'movement_date' => $med['started_at'] ?? now(),
                            'user_id' => $user->id,
                        ]);

                        $requiredQty -= $consumeQty;
                    }

                    // If not enough stock
                    if ($requiredQty > 0) {
                        throw new \Exception('Insufficient medicine stock for treatment.');
                    }
                }

                /* ----------------------------------
         | 4️⃣ Journal posting for medicine consumption
         |    DR 5002 Medicine Expense
         |    CR 1008 Medicine Inventory
         ---------------------------------- */
                $medicineTotal = (float) collect($validated['disease_treatment_medications'] ?? [])
                    ->sum(function ($m) {
                        $qty = (float) ($m['qty'] ?? 0);
                        $unitCost = isset($m['unit_cost']) ? (float) $m['unit_cost'] : 0.0;
                        $totalCost = isset($m['total_cost']) ? (float) $m['total_cost'] : null;

                        if ($totalCost === null) {
                            return $qty * $unitCost;
                        }

                        return (float) $totalCost;
                    });

                if ($medicineTotal > 0) {
                    $expenseAccount = ChartOfAccount::query()
                        ->where('farm_id', $user->farm_id)
                        ->where('code', '5002')
                        ->first();

                    if (!$expenseAccount) {
                        throw new \Exception('Chart of account 5002 (Medicine Expense) not found.');
                    }

                    $inventoryAccount = ChartOfAccount::query()
                        ->where('farm_id', $user->farm_id)
                        ->where('code', '1008')
                        ->first();

                    if (!$inventoryAccount) {
                        throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                    }

                    $entry = JournalEntry::query()->create([
                        'farm_id' => $user->farm_id,
                        'user_id' => $user->id,
                        'entry_date' => now(),
                        'reference_type' => 'disease_treatment',
                        'reference_id' => $treatment->id,
                        'description' => 'Medicine used for disease treatment #' . $treatment->id,
                        'status' => 'posted',
                        'created_by' => $user->id,
                    ]);

                    $entry->lines()->createMany([
                        [
                            'account_id' => $expenseAccount->id,
                            'debit_amount' => $medicineTotal,
                            'credit_amount' => 0,
                            'narration' => 'Medicine Expense',
                        ],
                        [
                            'account_id' => $inventoryAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $medicineTotal,
                            'narration' => 'Medicine Inventory',
                        ],
                    ]);
                }

                /* ----------------------------------
         | 5️⃣ Optional: resolve health event
         ---------------------------------- */
                if (!empty($validated['health_event_id']) && $validated['status'] === 'completed') {
                    HealthEvent::where('id', $validated['health_event_id'])
                        ->where('farm_id', $user->farm_id)
                        ->update(['resolved_at' => now()]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }


        return redirect()
            ->route('disease-treatments.index')
            ->with('success', 'Disease treatment recorded successfully.');
    }

    public function show(DiseaseTreatment $disease_treatment)
    {
        $disease_treatment->load(['healthIssue.animal', 'administeredBy', 'treatment', 'diseaseTreatmentMedications.medicine', 'healthEvent']);
        return Inertia::render('DiseaseTreatments/Show', ['treatment' => $disease_treatment]);
    }

    public function edit(DiseaseTreatment $disease_treatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $disease_treatment->load(['healthIssue.animal', 'diseaseTreatmentMedications.medicine', 'healthEvent']);

        // Format dates for date input
        $formattedTreatment = $disease_treatment->toArray();
        if ($disease_treatment->healthEvent && $disease_treatment->healthEvent->resolved_at) {
            $formattedTreatment['health_event']['resolved_at'] = $disease_treatment->healthEvent->resolved_at->format('Y-m-d');
        }
        foreach ($formattedTreatment['disease_treatment_medications'] as &$medication) {
            if (isset($medication['started_at'])) {
                $medication['started_at'] = \Carbon\Carbon::parse($medication['started_at'])->format('Y-m-d');
            }
            if (isset($medication['ended_at'])) {
                $medication['ended_at'] = \Carbon\Carbon::parse($medication['ended_at'])->format('Y-m-d');
            }
        }

        $healthIssues = HealthIssue::with('animal')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        // Fetch health events for the pre-selected health issue
        $healthEvents = [];
        if ($disease_treatment->health_issue_id) {
            $healthEvents = HealthEvent::where('health_issue_id', $disease_treatment->health_issue_id)
                ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                    $query->where('farm_id', $user->farm_id);
                })
                ->select('id', 'title', 'occurred_at')
                ->get();
        }

        return Inertia::render('DiseaseTreatments/Edit', [
            'treatment' => $formattedTreatment,
            'healthIssues' => $healthIssues,
            'staff' => $staff,
            'treatments' => Treatment::select('id', 'name')->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get(),
            'healthEvents' => $healthEvents,
            'allStaff' => $staff, // Pass all staff for medication line items
        ]);
    }

    public function update(Request $request, DiseaseTreatment $diseaseTreatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Block update if linked health event is resolved
        if ($diseaseTreatment->health_event_id) {
            $event = HealthEvent::find($diseaseTreatment->health_event_id);
            if ($event && $event->resolved_at) {
                return back()->withErrors([
                    'health_event_id' => 'Cannot update treatment linked to a resolved health event.'
                ]);
            }
        }

        $validated = $request->validate([
            'health_issue_id' => 'required|exists:health_issues,id',
            'health_event_id' => 'nullable|exists:health_events,id',
            'treatment_id' => 'required|exists:treatments,id',
            'description' => 'nullable|string',
            'resolved_at' => 'nullable|date',
            'status' => 'required|in:planned,ongoing,completed,discontinued',
            'notes' => 'nullable|string',
            'disease_treatment_medications' => 'nullable|array',
            'disease_treatment_medications.*.id' => 'nullable|exists:disease_treatment_medications,id',
            // For existing medications 
            'disease_treatment_medications.*.medicine_id' => 'required|exists:medicines,id',
            'disease_treatment_medications.*.dose' => 'nullable|string|max:100',
            'disease_treatment_medications.*.frequency' => 'nullable|string|max:100',
            'disease_treatment_medications.*.duration_days' => 'nullable|integer|min:0',
            'disease_treatment_medications.*.status' => 'required|in:planned,ongoing,completed,discontinued',
            'disease_treatment_medications.*.started_at' => 'nullable|date',
            'disease_treatment_medications.*.ended_at' => 'nullable|date|after_or_equal:disease_treatment_medications.*.started_at',
            'disease_treatment_medications.*.qty' => 'nullable|integer|min:0',
            'disease_treatment_medications.*.unit_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'disease_treatment_medications.*.total_cost' => 'nullable|numeric|min:0|max:9999999.99',
            'disease_treatment_medications.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $diseaseTreatment, $user) {

            /* -----------------------------
         | 1️⃣ Update Treatment Header
         ----------------------------- */
            $diseaseTreatment->update([
                'health_issue_id' => $validated['health_issue_id'],
                'health_event_id' => $validated['health_event_id'] ?? null,
                'treatment_id' => $validated['treatment_id'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $existingMeds = $diseaseTreatment->diseaseTreatmentMedications->keyBy('id');

            $existingMedicationIds = $diseaseTreatment->diseaseTreatmentMedications->pluck('id')->toArray();
            $updatedMedicationIds = [];


            /* ------------------------------------
         | 2️⃣ Sync medications (delta logic)
         ------------------------------------ */
            if (isset($validated['disease_treatment_medications'])) {
                foreach ($validated['disease_treatment_medications'] ?? [] as $medData) {

                    $oldQty = 0;
                    $medication = null;

                    if (!empty($medData['id'])) {
                        $medication = $existingMeds[$medData['id']];
                        $oldQty = $medication->qty;
                    }

                    $newQty = $medData['qty'];
                    $delta  = $newQty - $oldQty;

                    $unitCost = isset($medData['unit_cost']) ? (float) $medData['unit_cost'] : null;
                    $totalCost = isset($medData['total_cost']) ? (float) $medData['total_cost'] : null;

                    if ($unitCost !== null && $totalCost === null) {
                        $totalCost = (float) $newQty * $unitCost;
                    }

                    // Save or update medication record
                    $medication = $diseaseTreatment->diseaseTreatmentMedications()
                        ->updateOrCreate(
                            ['id' => $medData['id'] ?? null],
                            [
                                'medicine_id' => $medData['medicine_id'],
                                'qty' => $newQty,
                                'unit_cost' => $unitCost,
                                'total_cost' => $totalCost,
                                'started_at' => $medData['started_at'] ?? now(),
                                'notes' => $medData['notes'] ?? null,
                                'farm_id' => $user->farm_id,
                                'user_id' => $user->id,
                                'dose' => $medData['dose'] ?? null,
                                'frequency' => $medData['frequency'] ?? null,
                                'duration_days' => $medData['duration_days'] ?? null,
                                'status' => $medData['status'] ?? null,
                                'started_at' => $medData['started_at'] ?? now(),
                                'ended_at' => $medData['ended_at'] ?? null,
                                'notes' => $medData['notes'] ?? null,
                            ]
                        );

                    /* ------------------------------------
             | 3️⃣ Handle STOCK DELTA
             ------------------------------------ */

                    // ➕ Need MORE medicine → consume
                    if ($delta > 0) {

                        $required = $delta;

                        $batches = StockMovement::where('item_type', Medicine::class)
                            ->where('item_id', $medData['medicine_id'])
                            ->where('farm_id', $user->farm_id)
                            ->where('movement_type', 'in')
                            ->whereDate('expiry_date', '>=', now())
                            ->orderBy('expiry_date')
                            ->orderBy('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($batches as $batch) {

                            if ($required <= 0) break;

                            $consumed = StockMovement::where('movement_type', 'out')
                                ->where('item_type', Medicine::class)
                                ->where('item_id', $medData['medicine_id'])
                                ->where('batch_no', $batch->batch_no)
                                ->sum('quantity');

                            $available = $batch->quantity - $consumed;
                            if ($available <= 0) continue;

                            $useQty = min($required, $available);

                            StockMovement::create([
                                'farm_id' => $user->farm_id,
                                'item_type' => Medicine::class,
                                'item_id' => $medData['medicine_id'],
                                'movement_type' => 'out',
                                'source_event_type' => 'consumption',
                                'source_type' => DiseaseTreatment::class,
                                'source_id' => $diseaseTreatment->id,
                                'quantity' => $useQty,
                                'unit_cost' => $batch->unit_cost,
                                'batch_no' => $batch->batch_no,
                                'expiry_date' => $batch->expiry_date,
                                'movement_date' => now(),
                                'user_id' => $user->id,
                            ]);

                            $required -= $useQty;
                        }

                        if ($required > 0) {
                            throw new \Exception('Insufficient medicine stock.');
                        }
                    }

                    // ➖ Reduce quantity → return ONLY same batches
                    if ($delta < 0) {

                        $returnQty = abs($delta);

                        $outs = StockMovement::where('movement_type', 'out')
                            ->where('item_type', Medicine::class)
                            ->where('item_id', $medData['medicine_id'])
                            ->where('source_type', DiseaseTreatment::class)
                            ->where('source_id', $diseaseTreatment->id)
                            ->orderByDesc('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($outs as $out) {

                            if ($returnQty <= 0) break;

                            $rollbackQty = min($returnQty, $out->quantity);

                            // Reduce or delete OUT record
                            if ($rollbackQty === $out->quantity) {
                                $out->delete();
                            } else {
                                $out->decrement('quantity', $rollbackQty);
                            }

                            $returnQty -= $rollbackQty;
                        }
                    }

                    $updatedMedicationIds[] = $medication->id;
                }
            }

            /* ------------------------------------
         | 4️⃣ Delete removed medications (and revert stock)
         ------------------------------------ */
            $medicationsToDelete = array_diff($existingMedicationIds, $updatedMedicationIds);
            if (!empty($medicationsToDelete)) {
                foreach ($medicationsToDelete as $medicationId) {
                    $medication = $diseaseTreatment->diseaseTreatmentMedications()->find($medicationId);

                    if (!$medication) {
                        continue;
                    }

                    // Revert stock by rolling back existing OUT movements for this treatment+medicine
                    $returnQty = (float) ($medication->qty ?? 0);

                    if ($returnQty > 0) {
                        $outs = StockMovement::where('movement_type', 'out')
                            ->where('item_type', Medicine::class)
                            ->where('item_id', $medication->medicine_id)
                            ->where('source_type', DiseaseTreatment::class)
                            ->where('source_id', $diseaseTreatment->id)
                            ->orderByDesc('movement_date')
                            ->lockForUpdate()
                            ->get();

                        foreach ($outs as $out) {
                            if ($returnQty <= 0) {
                                break;
                            }

                            $rollbackQty = min($returnQty, (float) $out->quantity);

                            if ($rollbackQty == (float) $out->quantity) {
                                $out->delete();
                            } else {
                                $out->decrement('quantity', $rollbackQty);
                            }

                            $returnQty -= $rollbackQty;
                        }
                    }

                    $medication->delete();
                }
            }

            /* ------------------------------------
         | 5️⃣ Journal adjustment for medicine usage (delta)
         |    DR 5002 Medicine Expense
         |    CR 1008 Medicine Inventory
         ------------------------------------ */
            $oldMedicineTotal = (float) $existingMeds
                ->values()
                ->sum(function ($m) {
                    $qty = (float) ($m->qty ?? 0);
                    $unitCost = isset($m->unit_cost) ? (float) $m->unit_cost : 0.0;
                    $totalCost = isset($m->total_cost) ? (float) $m->total_cost : null;

                    if ($totalCost === null) {
                        return $qty * $unitCost;
                    }

                    return (float) $totalCost;
                });

            $newMedicineTotal = (float) collect($validated['disease_treatment_medications'] ?? [])
                ->sum(function ($m) {
                    $qty = (float) ($m['qty'] ?? 0);
                    $unitCost = isset($m['unit_cost']) ? (float) $m['unit_cost'] : 0.0;
                    $totalCost = isset($m['total_cost']) ? (float) $m['total_cost'] : null;

                    if ($totalCost === null) {
                        return $qty * $unitCost;
                    }

                    return (float) $totalCost;
                });

            $deltaMedicineTotal = $newMedicineTotal - $oldMedicineTotal;

            if ($deltaMedicineTotal != 0.0) {
                $expenseAccount = ChartOfAccount::query()
                    ->where('farm_id', $user->farm_id)
                    ->where('code', '5002')
                    ->first();

                if (!$expenseAccount) {
                    throw new \Exception('Chart of account 5002 (Medicine Expense) not found.');
                }

                $inventoryAccount = ChartOfAccount::query()
                    ->where('farm_id', $user->farm_id)
                    ->where('code', '1008')
                    ->first();

                if (!$inventoryAccount) {
                    throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                }

                $abs = abs((float) $deltaMedicineTotal);

                $entry = JournalEntry::query()->create([
                    'farm_id' => $user->farm_id,
                    'user_id' => $user->id,
                    'entry_date' => now(),
                    'reference_type' => 'disease_treatment',
                    'reference_id' => $diseaseTreatment->id,
                    'description' => 'Disease treatment medicine adjustment #' . $diseaseTreatment->id,
                    'status' => 'posted',
                    'created_by' => $user->id,
                ]);

                if ($deltaMedicineTotal > 0) {
                    // Increase usage: DR Expense, CR Inventory
                    $entry->lines()->createMany([
                        [
                            'account_id' => $expenseAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Medicine Expense (Adjustment)',
                        ],
                        [
                            'account_id' => $inventoryAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Medicine Inventory (Adjustment)',
                        ],
                    ]);
                } else {
                    // Decrease usage: DR Inventory, CR Expense
                    $entry->lines()->createMany([
                        [
                            'account_id' => $inventoryAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Medicine Inventory (Adjustment)',
                        ],
                        [
                            'account_id' => $expenseAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Medicine Expense (Adjustment)',
                        ],
                    ]);
                }
            }

            // If health_event_id and resolved_at are provided, update the health event 
            if (isset($validated['health_event_id']) && isset($validated['resolved_at'])) {
                $healthEvent = HealthEvent::find($validated['health_event_id']);
                if ($healthEvent && $healthEvent->farm_id === $user->farm_id) {
                    $healthEvent->update(['resolved_at' => $validated['resolved_at']]);
                }
            }
        });

        return redirect()
            ->route('disease-treatments.index')
            ->with('success', 'Disease treatment updated successfully.');
    }


    public function destroy(DiseaseTreatment $diseaseTreatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::transaction(function () use ($diseaseTreatment, $user) {

            /* ----------------------------------------
         | 1️⃣ Lock & delete stock OUT movements
         ---------------------------------------- */
            StockMovement::where('source_type', DiseaseTreatment::class)
                ->where('source_id', $diseaseTreatment->id)
                ->where('movement_type', 'out')
                ->lockForUpdate()
                ->delete();

            /* ----------------------------------------
         | 2️⃣ Delete medication usage records
         ---------------------------------------- */
            $diseaseTreatment->diseaseTreatmentMedications()->delete();

            /* ----------------------------------------
         | 3️⃣ Delete disease treatment
         ---------------------------------------- */
            $diseaseTreatment->delete();
        });

        return redirect()
            ->route('disease-treatments.index')
            ->with('success', 'Disease treatment deleted successfully.');
    }
}
