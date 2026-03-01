<?php

namespace App\Http\Controllers;

use App\Models\ArtificialInsemination;
use App\Models\ReproductionRecord;
use App\Models\User;
use App\Models\Animal; // Added Animal model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB; // Added DB facade
use App\Models\Setting; // Added Setting model

class ArtificialInseminationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ArtificialInsemination::class, 'artificial_insemination');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $artificialInseminations = ArtificialInsemination::with(['reproductionRecord.animal', 'vet'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('semen_batch_no', 'like', "%$q%")
                ->orWhereHas('breed', fn($bq) => $bq->where('name', 'like', "%$q%")) // Search by breed name
                ->orWhereHas('reproductionRecord.animal', fn($aq) => $aq->where('tag', 'like', "%$q%")))
            ->latest('insemination_date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = ArtificialInsemination::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $totalRecords = $baseQuery->count();
        $averageCost = $baseQuery->avg('cost');

        // Get currency setting
        $currency = Setting::first()->currency ?? '$'; // Default to '$' if not set

        $reproductionRecordsQuery = ReproductionRecord::whereIn('artificial_insemination_id', $baseQuery->pluck('id'));

        $successfulRecords = (clone $reproductionRecordsQuery)->where('outcome', 'successful')->count();
        $failedRecords = (clone $reproductionRecordsQuery)->where('outcome', 'failed')->count();
        $pendingRecords = (clone $reproductionRecordsQuery)->where('outcome', 'pending')->count();


        $statistics = [
            'total_records' => $totalRecords,
            'successful' => $successfulRecords,
            'failed' => $failedRecords,
            'pending' => $pendingRecords,
            'average_cost' => round($averageCost ?? 0, 2),
        ];

        return Inertia::render('ArtificialInseminations/Index', [
            'artificialInseminations' => $artificialInseminations,
            'filters' => $request->only('q'),
            'statistics' => $statistics, // Pass statistics to the view
            'currency' => $currency, // Pass currency to the view
        ]);
    }

    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }


        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet'); // Assuming a 'vet' role exists
            })
            ->get();

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        $breeds = \App\Models\Breed::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        return Inertia::render('ArtificialInseminations/Create', [
            'vets' => $vets,
            'animals' => $animals, // Pass animals to the view
            'breeds' => $breeds, // Pass breeds to the view
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
            'animal_id' => 'required|exists:animals,id', // New validation for animal_id
            //'event' => 'required|in:mating,insemination,pregnancy_check,calving,abortion,other', // New validation for event
            'semen_batch_no' => 'required|string|max:255',
            'breed_id' => 'required|exists:breeds,id', // New validation for breed_id
            'semen_company' => 'nullable|string|max:255',
            'insemination_date' => 'required|date',
            'vet_id' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $animal = Animal::findOrFail($validated['animal_id']);

        $validated['farm_id'] = $animal->farm_id; // Use animal's farm_id
        $validated['user_id'] = $user->id;

        $artificialInsemination = ArtificialInsemination::create($validated);

        return redirect()->route('artificial-inseminations.show', $artificialInsemination->id)->with('success', 'Artificial Insemination record created successfully.');
    }

    public function show(ArtificialInsemination $artificialInsemination)
    {
        $artificialInsemination->load(['reproductionRecord.animal', 'vet']);
        return Inertia::render('ArtificialInseminations/Show', ['artificialInsemination' => $artificialInsemination]);
    }

    public function edit(ArtificialInsemination $artificialInsemination)
    {

        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $vets = User::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereHas('roles', function ($query) {
                $query->where('name', 'vet');
            })
            ->get();

        $artificialInsemination->load(['reproductionRecord.animal', 'vet', 'breed']);

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        $breeds = \App\Models\Breed::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get();

        // Add animal_id and event to artificialInsemination for form pre-filling
        $artificialInsemination->animal_id = $artificialInsemination->reproductionRecord->animal_id ?? null;
        $artificialInsemination->event = $artificialInsemination->reproductionRecord->event ?? null;


        return Inertia::render('ArtificialInseminations/Edit', [
            'artificialInsemination' => $artificialInsemination,
            'vets' => $vets,
            'animals' => $animals, // Pass animals to the view
            'breeds' => $breeds, // Pass breeds to the view
        ]);
    }

    public function update(Request $request, ArtificialInsemination $artificialInsemination)
    {

        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'animal_id' => 'sometimes|required|exists:animals,id', // New validation for animal_id
            //'event' => 'sometimes|required|in:mating,insemination,pregnancy_check,calving,abortion,other', // New validation for event
            'semen_batch_no' => 'required|string|max:255',
            'breed_id' => 'sometimes|required|exists:breeds,id', // New validation for breed_id
            'semen_company' => 'nullable|string|max:255',
            'insemination_date' => 'required|date',
            'vet_id' => 'nullable|exists:users,id',
            'cost' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        $artificialInsemination->update($validated);

        return redirect()->route('artificial-inseminations.show', $artificialInsemination->id)->with('success', 'Artificial Insemination record updated successfully.');
    }

    public function destroy(ArtificialInsemination $artificialInsemination)
    {
        // The associated ReproductionRecord will be nullOnDelete, so no need to explicitly delete it here.
        // We also don't need to redirect to reproduction-records.show as the AI record is the primary entity here.
        $artificialInsemination->delete();
        return redirect()->route('artificial-inseminations.index')->with('success', 'Artificial Insemination record deleted successfully.');
    }
}
