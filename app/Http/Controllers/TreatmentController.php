<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Farm; // Import Farm model
use App\Models\Medication;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;
use App\Models\User; // Import User model for type hinting
use App\Models\StockMovement; // Import StockMovement model
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\Supplier; // Import Supplier model

class TreatmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Treatment::class, 'treatment');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $treatments = Treatment::with(['medications.medicine'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhereHas('medications.medicine', fn($qbm) => $qbm->where('name', 'like', "%$q%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = Treatment::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_treatments' => (clone $baseQuery)->count(),
            'with_medications' => (clone $baseQuery)->has('medications')->count(),
            'avg_medication_duration' => Medication::whereIn('treatment_id', (clone $baseQuery)->pluck('id'))->avg('duration_days'),
        ];

        return Inertia::render('Treatments/Index', [
            'treatments' => $treatments,
            'statistics' => $statistics,
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
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $medicines = Medicine::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Treatments/Create', [
            'farms' => $farms,
            'medicines' => $medicines,
            'suppliers' => $suppliers,
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
            'name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'medications' => 'array',
            'medications.*.medicine_id' => 'required_with:medications|exists:medicines,id',
            'medications.*.dose' => 'required_with:medications|string|max:100',
            'medications.*.frequency' => 'required_with:medications|string|max:100',
            'medications.*.duration_days' => 'required_with:medications|integer|min:1',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        } else {
            // If not a farm owner, farm_id must be provided and validated
            $request->validate(['farm_id' => 'required|exists:farms,id']);
            $validated['farm_id'] = $request->input('farm_id');
            $validated['user_id'] = $user->id;
        }

        DB::beginTransaction();
        try {
            $treatment = Treatment::create([
                'name' => $validated['name'],
                'instructions' => $validated['instructions'],
                'farm_id' => $validated['farm_id'],
                'user_id' => $validated['user_id']
            ]);

            if (!empty($validated['medications'])) {
                foreach ($validated['medications'] as $medicationData) {
                    $medication = $treatment->medications()->create($medicationData);

                    // Record stock movement for medicine consumption
                    if (isset($medicationData['qty']) && $medicationData['qty'] > 0) {
                        StockMovement::create([
                            'farm_id' => $treatment->farm_id,
                            'item_type' => Medicine::class,
                            'item_id' => $medicationData['medicine_id'],
                            'movement_type' => 'out',
                            'source_event_type' => 'consumption',
                            'source_id' => $medication->id, // Link to the specific medication record
                            'source_type' => Medication::class,
                            'quantity' => $medicationData['qty'],
                            'unit_cost' => 0, // Assuming unit_cost is not directly available here
                            'movement_date' => now()->toDateString(),
                            'user_id' => $user->id,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('treatments.index')->with('success', 'Treatment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create treatment: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Treatment $treatment)
    {
        $treatment->load('medications.medicine');
        return Inertia::render('Treatments/Show', ['treatment' => $treatment]);
    }

    public function edit(Treatment $treatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $medicines = Medicine::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $treatment->load('medications.medicine');

        return Inertia::render('Treatments/Edit', [
            'treatment' => $treatment,
            'farms' => $farms,
            'medicines' => $medicines,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Treatment $treatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'medications' => 'array',
            'medications.*.medicine_id' => 'required_with:medications|exists:medicines,id',
            'medications.*.dose' => 'required_with:medications|string|max:100',
            'medications.*.frequency' => 'required_with:medications|string|max:100',
            'medications.*.duration_days' => 'required_with:medications|integer|min:1',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        } else {
            // If not a farm owner, farm_id must be provided and validated
            $request->validate(['farm_id' => 'required|exists:farms,id']);
            $validated['farm_id'] = $request->input('farm_id');
        }

        $treatment->update([
            'name' => $validated['name'],
            'instructions' => $validated['instructions'],
            'farm_id' => $validated['farm_id'],
        ]);

        // Delete existing medications and create new ones
        $treatment->medications()->delete();
        if (!empty($validated['medications'])) {
            foreach ($validated['medications'] as $medicationData) {
                $treatment->medications()->create($medicationData);
            }
        }

        DB::beginTransaction();
        try {
            $treatment->update([
                'name' => $validated['name'],
                'instructions' => $validated['instructions'],
                'farm_id' => $validated['farm_id'],
            ]);

            // Get existing medications for stock adjustment
            $oldMedications = $treatment->medications->keyBy('id');
            $updatedMedicationIds = [];

            if (!empty($validated['medications'])) {
                foreach ($validated['medications'] as $medicationData) {
                    if (isset($medicationData['id'])) {
                        // Update existing medication
                        $medication = $oldMedications->pull($medicationData['id']);
                        if ($medication) {
                            $oldQty = $medication->qty;
                            $newQty = $medicationData['qty'] ?? 0;

                            $medication->update($medicationData);
                            $updatedMedicationIds[] = $medication->id;

                            if ($newQty !== $oldQty) {
                                if ($oldQty > 0) {
                                    // Revert old stock movement
                                    StockMovement::create([
                                        'farm_id' => $treatment->farm_id,
                                        'item_type' => Medicine::class,
                                        'item_id' => $medication->medicine_id,
                                        'movement_type' => 'in',
                                        'source_event_type' => 'consumption_adjustment',
                                        'source_id' => $medication->id,
                                        'source_type' => Medication::class,
                                        'quantity' => $oldQty,
                                        'unit_cost' => 0,
                                        'movement_date' => now()->toDateString(),
                                        'user_id' => $user->id,
                                    ]);
                                }
                                if ($newQty > 0) {
                                    // Apply new stock movement
                                    StockMovement::create([
                                        'farm_id' => $treatment->farm_id,
                                        'item_type' => Medicine::class,
                                        'item_id' => $medicationData['medicine_id'],
                                        'movement_type' => 'out',
                                        'source_event_type' => 'consumption_adjustment',
                                        'source_id' => $medication->id,
                                        'source_type' => Medication::class,
                                        'quantity' => $newQty,
                                        'unit_cost' => 0,
                                        'movement_date' => now()->toDateString(),
                                        'user_id' => $user->id,
                                    ]);
                                }
                            }
                        }
                    } else {
                        // Create new medication
                        $newMedication = $treatment->medications()->create($medicationData);
                        $updatedMedicationIds[] = $newMedication->id;

                        if (isset($medicationData['qty']) && $medicationData['qty'] > 0) {
                            StockMovement::create([
                                'farm_id' => $treatment->farm_id,
                                'item_type' => Medicine::class,
                                'item_id' => $medicationData['medicine_id'],
                                'movement_type' => 'out',
                                'source_event_type' => 'consumption',
                                'source_id' => $newMedication->id,
                                'source_type' => Medication::class,
                                'quantity' => $medicationData['qty'],
                                'unit_cost' => 0,
                                'movement_date' => now()->toDateString(),
                                'user_id' => $user->id,
                            ]);
                        }
                    }
                }
            }

            // Delete medications that were removed from the list
            foreach ($oldMedications as $medication) {
                if (!in_array($medication->id, $updatedMedicationIds)) {
                    if ($medication->qty > 0) {
                        // Revert stock movement for deleted medication
                        StockMovement::create([
                            'farm_id' => $treatment->farm_id,
                            'item_type' => Medicine::class,
                            'item_id' => $medication->medicine_id,
                            'movement_type' => 'in',
                            'source_event_type' => 'consumption_reversal',
                            'source_id' => $medication->id,
                            'source_type' => Medication::class,
                            'quantity' => $medication->qty,
                            'unit_cost' => 0,
                            'movement_date' => now()->toDateString(),
                            'user_id' => $user->id,
                        ]);
                    }
                    $medication->delete();
                }
            }

            DB::commit();
            return redirect()->route('treatments.index')->with('success', 'Treatment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update treatment: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Treatment $treatment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            foreach ($treatment->medications as $medication) {
                if ($medication->qty > 0) {
                    // Revert stock movement for deleted medication
                    StockMovement::create([
                        'farm_id' => $treatment->farm_id,
                        'item_type' => Medicine::class,
                        'item_id' => $medication->medicine_id,
                        'movement_type' => 'in',
                        'source_event_type' => 'consumption_reversal',
                        'source_id' => $medication->id,
                        'source_type' => Medication::class,
                        'quantity' => $medication->qty,
                        'unit_cost' => 0,
                        'movement_date' => now()->toDateString(),
                        'user_id' => $user->id,
                    ]);
                }
            }
            $treatment->delete();
            DB::commit();
            return redirect()->route('treatments.index')->with('success', 'Treatment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete treatment: ' . $e->getMessage()])->withInput();
        }
    }

    public function getMedicationsByTreatment(Treatment $treatment)
    {
        $this->authorize('view', $treatment); // Ensure user can view this treatment

        $medications = $treatment->medications()->with('medicine')->get()->map(function ($medication) {
            return [
                'id' => $medication->id,
                'medicine_id' => $medication->medicine_id,
                'medicine_name' => $medication->medicine->name,
                'dose' => $medication->dose,
                'frequency' => $medication->frequency,
                'duration_days' => $medication->duration_days,
            ];
        });

        return response()->json($medications);
    }
}
