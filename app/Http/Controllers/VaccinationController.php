<?php

namespace App\Http\Controllers;

use App\Models\VaccinationRecord;
use App\Models\Animal;
use App\Models\VaccineType;
use App\Models\StaffProfile; // Import StaffProfile
use App\Models\Farm; // Import Farm model
use App\Models\Disease; // Import Disease model
use App\Models\Medicine; // Import Medicine model
use App\Data\VaccinationData;
use App\Models\VaccinationMedication;
use Illuminate\Http\Request; // Import Request class
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;
use Illuminate\Support\Facades\DB; // Import DB facade
use Carbon\Carbon; // Import Carbon for date formatting

class VaccinationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(VaccinationRecord::class, 'vaccination');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $vaccinations = VaccinationRecord::with(['animal', 'staff', 'farm', 'disease', 'medications.medicine']) // Eager load 'farm', 'disease', and 'medications'
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return Inertia::render('Vaccinations/Index', [
            'vaccinations' => $vaccinations,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $staff = StaffProfile::select('id', 'first_name', 'last_name', 'position')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'position' => $s->position ?? 'Staff'
                ];
            });

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $diseases = Disease::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $medicines = Medicine::select('id', 'name', 'unit')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Vaccinations/Create', [
            'animals' => $animals,
            'staff' => $staff,
            'farms' => $farms,
            'diseases' => $diseases,
            'medicines' => $medicines,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'administered_at' => 'nullable|date',
            'next_due_at' => 'nullable|date',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
            'medications' => 'array',
            'medications.*.medicine_id' => 'required_with:medications|exists:medicines,id',
            'medications.*.quantity' => 'required_with:medications|numeric|min:0.01',
            'medications.*.dose' => 'nullable|string|max:100',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $staffProfile = StaffProfile::find($validated['staff_id']);
            if ($staffProfile && $staffProfile->farm_id != $user->farm_id) {
                return back()->withErrors(['staff_id' => 'The selected staff member does not belong to your farm.'])->withInput();
            }

            $disease = Disease::find($validated['disease_id']);
            if ($disease && $disease->farm_id != $user->farm_id) {
                return back()->withErrors(['disease_id' => 'The selected disease does not belong to your farm.'])->withInput();
            }

            if (isset($validated['medications'])) {
                foreach ($validated['medications'] as $medication) {
                    $medicine = Medicine::find($medication['medicine_id']);
                    if ($medicine && $medicine->farm_id != $user->farm_id) {
                        return back()->withErrors(['medications' => 'One or more selected medicines do not belong to your farm.'])->withInput();
                    }
                }
            }
        }
        // Ensure farm_id is set for the vaccination record if the user has one
        if ($user->farm_id) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        try {
            DB::transaction(function () use ($validated, $user) {
                $vaccinationRecord = VaccinationRecord::create(VaccinationData::from($validated)->toArray());

                foreach ($validated['medications'] ?? [] as $med) {
                    $vaccinationRecord->medications()->create([
                        'medicine_id' => $med['medicine_id'],
                        'quantity' => $med['quantity'],
                        'dose' => $med['dose'] ?? null,
                    ]);

                    // Handle stock movement (OUT)
                    $requiredQty = $med['quantity'];
                    $inBatches = \App\Models\StockMovement::where('item_type', Medicine::class)
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

                        $alreadyConsumed = \App\Models\StockMovement::where('item_type', Medicine::class)
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

                        \App\Models\StockMovement::create([
                            'farm_id' => $user->farm_id,
                            'item_type' => Medicine::class,
                            'item_id' => $med['medicine_id'],
                            'movement_type' => 'out',
                            'source_event_type' => 'consumption',
                            'source_type' => VaccinationRecord::class,
                            'source_id' => $vaccinationRecord->id,
                            'quantity' => $consumeQty,
                            'unit_cost' => $batch->unit_cost,
                            'batch_no' => $batch->batch_no,
                            'expiry_date' => $batch->expiry_date,
                            'movement_date' => $validated['administered_at'] ?? now(),
                            'user_id' => $user->id,
                        ]);

                        $requiredQty -= $consumeQty;
                    }

                    if ($requiredQty > 0) {
                        throw new \Exception('Insufficient medicine stock for vaccination.');
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('vaccinations.index')->with('success', 'Vaccination recorded.');
    }

    public function show(VaccinationRecord $vaccination)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $vaccination->load(['animal', 'staff', 'farm', 'disease', 'medications.medicine']); // Eager load 'farm', 'disease', and 'medications'
        // Format dates for date input
        $formattedVaccination = $vaccination->toArray();
        if ($vaccination->administered_at) {
            $formattedVaccination['administered_at'] = Carbon::parse($vaccination->administered_at)->format('Y-m-d\TH:i');
        }
        if ($vaccination->next_due_at) {
            $formattedVaccination['next_due_at'] = Carbon::parse($vaccination->next_due_at)->format('Y-m-d\TH:i');
        }
        foreach ($formattedVaccination['medications'] as &$medication) {
            if (isset($medication['administered_at'])) {
                $medication['administered_at'] = Carbon::parse($medication['administered_at'])->format('Y-m-d');
            }
        }
        return Inertia::render('Vaccinations/Show', ['vaccination' => $formattedVaccination]);
    }

    public function edit(VaccinationRecord $vaccination)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $vaccination->load(['animal', 'staff', 'farm', 'disease', 'medications.medicine']); // Eager load 'farm', 'disease', and 'medications'

        $staff = StaffProfile::select('id', 'first_name', 'last_name', 'position')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->orderBy('first_name')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => trim($s->first_name . ' ' . $s->last_name),
                    'position' => $s->position ?? 'Staff'
                ];
            });

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $diseases = Disease::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $medicines = Medicine::select('id', 'name', 'unit')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Vaccinations/Edit', [
            'vaccination' => $vaccination,
            'animals' => $animals,
            'staff' => $staff,
            'farms' => $farms,
            'diseases' => $diseases,
            'medicines' => $medicines,
        ]);
    }

    public function update(Request $request, VaccinationRecord $vaccination)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'disease_id' => 'nullable|exists:diseases,id',
            'administered_at' => 'nullable|date',
            'next_due_at' => 'nullable|date',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
            'medications' => 'array',
            'medications.*.medicine_id' => 'required_with:medications|exists:medicines,id',
            'medications.*.quantity' => 'required_with:medications|numeric|min:0.01',
            'medications.*.dose' => 'nullable|string|max:100',
            'medications.*.id' => 'nullable|exists:vaccination_medications,id', // For existing medications
        ]);


        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $staffProfile = StaffProfile::find($validated['staff_id']);
            if ($staffProfile && $staffProfile->farm_id != $user->farm_id) {
                return back()->withErrors(['staff_id' => 'The selected staff member does not belong to your farm.'])->withInput();
            }

            $disease = Disease::find($validated['disease_id']);
            if ($disease && $disease->farm_id != $user->farm_id) {
                return back()->withErrors(['disease_id' => 'The selected disease does not belong to your farm.'])->withInput();
            }

            if (isset($validated['medications'])) {
                foreach ($validated['medications'] as $medication) {
                    $medicine = Medicine::find($medication['medicine_id']);
                    if ($medicine && $medicine->farm_id != $user->farm_id) {
                        return back()->withErrors(['medications' => 'One or more selected medicines do not belong to your farm.'])->withInput();
                    }
                }
            }
        }
        // Ensure farm_id is set for the vaccination record if the user has one
        if ($user->farm_id) {
            $validated['farm_id'] = $user->farm_id;
        }

        try {
            DB::transaction(function () use ($validated, $vaccination, $user) {
                $vaccination->update(VaccinationData::from($validated)->toArray());

                $existingMeds = $vaccination->medications->keyBy('id');
                $updatedMedicationIds = [];

                foreach ($validated['medications'] ?? [] as $medData) {
                    $oldQty = 0;
                    $medication = null;

                    if (!empty($medData['id'])) {
                        $medication = $existingMeds[$medData['id']];
                        $oldQty = $medication->quantity;
                    }

                    $newQty = $medData['quantity'];
                    $delta = $newQty - $oldQty;

                    $medication = $vaccination->medications()->updateOrCreate(
                        ['id' => $medData['id'] ?? null],
                        [
                            'medicine_id' => $medData['medicine_id'],
                            'quantity' => $newQty,
                            'dose' => $medData['dose'] ?? null,
                        ]
                    );

                    // Handle stock delta
                    if ($delta > 0) {
                        $required = $delta;
                        $batches = \App\Models\StockMovement::where('item_type', Medicine::class)
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

                            $consumed = \App\Models\StockMovement::where('movement_type', 'out')
                                ->where('item_type', Medicine::class)
                                ->where('item_id', $medData['medicine_id'])
                                ->where('batch_no', $batch->batch_no)
                                ->sum('quantity');

                            $available = $batch->quantity - $consumed;
                            if ($available <= 0) continue;

                            $useQty = min($required, $available);

                            \App\Models\StockMovement::create([
                                'farm_id' => $user->farm_id,
                                'item_type' => Medicine::class,
                                'item_id' => $medData['medicine_id'],
                                'movement_type' => 'out',
                                'source_event_type' => 'consumption',
                                'source_type' => VaccinationRecord::class,
                                'source_id' => $vaccination->id,
                                'quantity' => $useQty,
                                'unit_cost' => $batch->unit_cost,
                                'batch_no' => $batch->batch_no,
                                'expiry_date' => $batch->expiry_date,
                                'movement_date' => $validated['administered_at'] ?? now(),
                                'user_id' => $user->id,
                            ]);

                            $required -= $useQty;
                        }

                        if ($required > 0) {
                            throw new \Exception('Insufficient medicine stock for vaccination.');
                        }
                    } elseif ($delta < 0) {
                        $returnQty = abs($delta);
                        $outs = \App\Models\StockMovement::where('movement_type', 'out')
                            ->where('item_type', Medicine::class)
                            ->where('item_id', $medData['medicine_id'])
                            ->where('source_type', VaccinationRecord::class)
                            ->where('source_id', $vaccination->id)
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
                    $updatedMedicationIds[] = $medication->id;
                }

                // Delete medications not in the submitted list
                \App\Models\VaccinationMedication::where('vaccination_record_id', $vaccination->id)
                    ->whereNotIn('id', $updatedMedicationIds)
                    ->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('vaccinations.index')->with('success', 'Vaccination record updated.');
    }

    public function destroy(VaccinationRecord $vaccination)
    {
        /** @var \App\Models\User&\Spatie\Permission\Traits\HasRoles $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            DB::transaction(function () use ($vaccination, $user) {
                // Revert stock movements for deleted medications
                \App\Models\StockMovement::where('source_type', VaccinationRecord::class)
                    ->where('source_id', $vaccination->id)
                    ->where('movement_type', 'out')
                    ->lockForUpdate()
                    ->delete();

                $vaccination->medications()->delete();
                $vaccination->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
        return redirect()->route('vaccinations.index')->with('success', 'Vaccination record deleted.');
    }
}
