<?php

namespace App\Http\Controllers;

use App\Models\ReproductionRecord;
use App\Models\Animal;
use App\Models\Farm; // Import Farm model
use App\Models\ArtificialInsemination; // Import ArtificialInsemination model
use Illuminate\Http\Request;
use App\Models\User; // Import User model
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;

class ReproductionRecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ReproductionRecord::class, 'reproduction_record');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $records = ReproductionRecord::with(['animal', 'partner'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%")))
            ->latest('event_date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = ReproductionRecord::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_records' => (clone $baseQuery)->count(),
            'successful' => (clone $baseQuery)->where('outcome', 'successful')->count(),
            'failed' => (clone $baseQuery)->where('outcome', 'failed')->count(),
            'pending' => (clone $baseQuery)->where('outcome', 'pending')->count(),
        ];

        return Inertia::render('ReproductionRecords/Index', [
            'records' => $records,
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
        $animals = Animal::select('id', 'tag', 'name', 'sex')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $users = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet'); // Assuming a 'vet' role exists
            })
            ->get();

        $breeds = \App\Models\Breed::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        return Inertia::render('ReproductionRecords/Create', [
            'animals' => $animals,
            'farms' => $farms,
            'users' => $users,
            'breeds' => $breeds,
            'vets' => $vets, // Pass users as vets
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'animal_id' => 'required|exists:animals,id',
                'event' => 'required|string|max:100',
                'partner_id' => 'nullable|exists:animals,id',
                'event_date' => 'required|date',
                'outcome' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'heat_stage' => 'nullable|in:early,optimal,late',
                'performed_by' => 'nullable|exists:users,id',
                'artificial_insemination_id' => 'nullable|exists:artificial_inseminations,id', // Add AI ID validation
                'remarks' => 'nullable|string',
            ]);

            // Enforce farm_id for farm owners
            if ($user->hasRole('farm owner')) {
                $animal = Animal::find($validated['animal_id']);
                if ($animal && $animal->farm_id != $user->farm_id) {
                    DB::rollBack();
                    return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
                }
                if (!empty($validated['partner_id'])) {
                    $partner = Animal::find($validated['partner_id']);
                    if ($partner && $partner->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['partner_id' => 'The selected partner animal does not belong to your farm.'])->withInput();
                    }
                }
                $validated['farm_id'] = $user->farm_id;
                $validated['user_id'] = $user->id;
            } else {
                // For non-farm-owner users (e.g. staff with permissions), infer farm_id from the selected animal.
                $farmId = Animal::whereKey($validated['animal_id'])->value('farm_id');
                if (!$farmId) {
                    DB::rollBack();
                    return back()->withErrors(['animal_id' => 'Unable to determine farm for this reproduction record.'])->withInput();
                }
                $validated['farm_id'] = $farmId;
                $validated['user_id'] = $user->id;
            }

            $reproductionRecord = ReproductionRecord::create($validated);

            // Associate performed_by with the current user if not explicitly set and user is logged in
            if (empty($validated['performed_by']) && Auth::check()) {
                $reproductionRecord->performed_by = Auth::id();
                $reproductionRecord->save();
            }

            $aiCost = 0.0;

            // Handle Artificial Insemination record creation if event is 'artificial_insemination'
            if ($validated['event'] === 'artificial_insemination') {
                $aiData = $request->validate([
                    'semen_batch_no' => 'required|string|max:255',
                    'breed_id' => 'required_if:event,artificial_insemination|exists:breeds,id',
                    'semen_company' => 'nullable|string|max:255',
                    'insemination_date' => 'required|date',
                    'vet_id' => 'nullable|exists:users,id',
                    'cost' => 'nullable|numeric|min:0',
                    'remarks' => 'nullable|string',
                ]);
                $aiData['farm_id'] = $reproductionRecord->farm_id;
                $aiData['user_id'] = $user->id;
                $aiData['reproduction_record_id'] = $reproductionRecord->id;

                $artificialInsemination = ArtificialInsemination::create($aiData);
                $reproductionRecord->artificial_insemination_id = $artificialInsemination->id;
                $reproductionRecord->save();

                $aiCost = (float) ($aiData['cost'] ?? 0);
            }

            // Auto journal integration for Artificial Insemination:
            // DR 5012 - Artificial Insemination Expense
            // CR 1008 - Medicine Inventory
            if ($validated['event'] === 'artificial_insemination' && $aiCost > 0) {
                $expenseAccount = ChartOfAccount::query()->where('code', '5012')->first();
                if (!$expenseAccount) {
                    throw new \Exception('Chart of account 5012 (Artificial Insemination Expense) not found.');
                }

                $medicineInventoryAccount = ChartOfAccount::query()->where('code', '1008')->first();
                if (!$medicineInventoryAccount) {
                    throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                }

                $entry = JournalEntry::query()->create([
                    'farm_id' => $reproductionRecord->farm_id,
                    'user_id' => $reproductionRecord->user_id,
                    'entry_date' => $reproductionRecord->event_date,
                    'reference_type' => 'reproduction_record',
                    'reference_id' => $reproductionRecord->id,
                    'description' => 'Artificial insemination expense (Reproduction Record #' . $reproductionRecord->id . ')',
                    'status' => 'posted',
                    'created_by' => $reproductionRecord->user_id,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_id' => $expenseAccount->id,
                        'debit_amount' => $aiCost,
                        'credit_amount' => 0,
                        'narration' => 'Artificial Insemination Expense',
                    ],
                    [
                        'account_id' => $medicineInventoryAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $aiCost,
                        'narration' => 'Medicine Inventory',
                    ],
                ]);
            }

            DB::commit();
            return redirect()->route('reproduction-records.index')->with('success', 'Reproduction record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create reproduction record: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(ReproductionRecord $reproductionRecord)
    {
        $reproductionRecord->load(['animal', 'partner', 'performer', 'artificialInsemination.vet']);
        return Inertia::render('ReproductionRecords/Show', ['record' => $reproductionRecord]);
    }

    public function edit(ReproductionRecord $reproductionRecord)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $reproductionRecord->load('artificialInsemination.breed'); // Eager load artificialInsemination and its nested breed

        $animals = Animal::select('id', 'tag', 'name', 'sex')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $users = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet'); // Assuming a 'vet' role exists
            })
            ->get();

        $breeds = \App\Models\Breed::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        return Inertia::render('ReproductionRecords/Edit', [
            'record' => $reproductionRecord,
            'animals' => $animals,
            'breeds' => $breeds,
            'farms' => $farms,
            'users' => $users,
            'vets' => $vets, // Pass users as vets
        ]);
    }

    public function update(Request $request, ReproductionRecord $reproductionRecord)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        DB::beginTransaction();
        try {
            $oldEvent = $reproductionRecord->event;
            $oldEventDate = $reproductionRecord->event_date;

            $oldAiCost = (float) ($reproductionRecord->artificialInsemination?->cost ?? 0);

            $validated = $request->validate([
                'animal_id' => 'required|exists:animals,id',
                'partner_id' => 'nullable|exists:animals,id',
                'event_date' => 'required|date',
                'event' => 'required|string|max:100',
                'outcome' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'heat_stage' => 'nullable|in:early,optimal,late',
                'performed_by' => 'nullable|exists:users,id',
                'artificial_insemination_id' => 'nullable|exists:artificial_inseminations,id', // Add AI ID validation
            ]);

            // Enforce farm_id for farm owners
            if ($user->hasRole('farm owner')) {
                $animal = Animal::find($validated['animal_id']);
                if ($animal && $animal->farm_id != $user->farm_id) {
                    DB::rollBack();
                    return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
                }
                if (!empty($validated['partner_id'])) {
                    $partner = Animal::find($validated['partner_id']);
                    if ($partner && $partner->farm_id != $user->farm_id) {
                        DB::rollBack();
                        return back()->withErrors(['partner_id' => 'The selected partner animal does not belong to your farm.'])->withInput();
                    }
                }
                $validated['farm_id'] = $user->farm_id;
            }

            $reproductionRecord->update($validated);

            // Associate performed_by with the current user if not explicitly set and user is logged in
            if (empty($validated['performed_by']) && Auth::check()) {
                $reproductionRecord->performed_by = Auth::id();
                $reproductionRecord->save();
            }

            $newAiCost = 0.0;

            // Handle Artificial Insemination record creation/update if event is 'artificial_insemination'
            if ($validated['event'] === 'artificial_insemination') {
                $aiData = $request->validate([
                    'semen_batch_no' => 'required|string|max:255',
                    'breed_id' => 'required|exists:breeds,id',
                    'semen_company' => 'nullable|string|max:255',
                    'insemination_date' => 'required|date',
                    'vet_id' => 'nullable|exists:users,id',
                    'cost' => 'nullable|numeric|min:0',
                    'remarks' => 'nullable|string',
                ]);

                if ($reproductionRecord->artificialInsemination) {
                    $reproductionRecord->artificialInsemination->update($aiData);
                } else {
                    $aiData['farm_id'] = $reproductionRecord->farm_id;
                    $aiData['user_id'] = $user->id;
                    $aiData['reproduction_record_id'] = $reproductionRecord->id;

                    $artificialInsemination = ArtificialInsemination::create($aiData);
                    $reproductionRecord->artificial_insemination_id = $artificialInsemination->id;
                    $reproductionRecord->save();
                }

                $newAiCost = (float) ($aiData['cost'] ?? 0);
            } else {
                // If event changed from AI to something else, delete associated AI record
                if ($reproductionRecord->artificialInsemination) {
                    $reproductionRecord->artificialInsemination->delete();
                    $reproductionRecord->artificial_insemination_id = null;
                    $reproductionRecord->save();
                }
            }

            // Journal adjustments for AI event:
            // - If previously AI and now not AI: reverse old cost
            // - If now AI and previously not AI: post new cost
            // - If both AI: post delta (new - old)
            $wasAi = $oldEvent === 'artificial_insemination';
            $isAi = $validated['event'] === 'artificial_insemination';

            $delta = 0.0;
            if ($wasAi && !$isAi) {
                $delta = -$oldAiCost;
            } elseif (!$wasAi && $isAi) {
                $delta = $newAiCost;
            } elseif ($wasAi && $isAi) {
                $delta = $newAiCost - $oldAiCost;
            }

            if ($delta != 0.0) {
                $expenseAccount = ChartOfAccount::query()->where('code', '5012')->first();
                if (!$expenseAccount) {
                    throw new \Exception('Chart of account 5012 (Artificial Insemination Expense) not found.');
                }

                $medicineInventoryAccount = ChartOfAccount::query()->where('code', '1008')->first();
                if (!$medicineInventoryAccount) {
                    throw new \Exception('Chart of account 1008 (Medicine Inventory) not found.');
                }

                $abs = abs($delta);

                $entry = JournalEntry::query()->create([
                    'farm_id' => $reproductionRecord->farm_id,
                    'user_id' => $user->id,
                    'entry_date' => $validated['event_date'] ?? $oldEventDate,
                    'reference_type' => 'reproduction_record',
                    'reference_id' => $reproductionRecord->id,
                    'description' => 'Artificial insemination adjustment (Reproduction Record #' . $reproductionRecord->id . ')',
                    'status' => 'posted',
                    'created_by' => $user->id,
                ]);

                if ($delta > 0) {
                    // Increased AI cost: DR Expense, CR Medicine Inventory
                    $entry->lines()->createMany([
                        [
                            'account_id' => $expenseAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Artificial Insemination Expense (Adjustment)',
                        ],
                        [
                            'account_id' => $medicineInventoryAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Medicine Inventory (Adjustment)',
                        ],
                    ]);
                } else {
                    // Decreased AI cost / removed AI: DR Medicine Inventory, CR Expense
                    $entry->lines()->createMany([
                        [
                            'account_id' => $medicineInventoryAccount->id,
                            'debit_amount' => $abs,
                            'credit_amount' => 0,
                            'narration' => 'Medicine Inventory (Adjustment)',
                        ],
                        [
                            'account_id' => $expenseAccount->id,
                            'debit_amount' => 0,
                            'credit_amount' => $abs,
                            'narration' => 'Artificial Insemination Expense (Adjustment)',
                        ],
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reproduction-records.index')->with('success', 'Reproduction record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update reproduction record: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(ReproductionRecord $reproductionRecord)
    {
        $reproductionRecord->delete();
        return redirect()->route('reproduction-records.index')->with('success', 'Reproduction record deleted successfully.');
    }
}
