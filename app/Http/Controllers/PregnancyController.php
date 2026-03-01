<?php

namespace App\Http\Controllers;

use App\Models\Pregnancy;
use App\Models\Animal;
use App\Models\ReproductionRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // For date calculations
use App\Models\PregnancyCheckup;
use App\Models\CalvingRecord;
use App\Enums\CheckupResult;

class PregnancyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Pregnancy::class, 'pregnancy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $pregnancies = Pregnancy::with(['animal', 'reproductionRecord'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%")->orWhere('name', 'like', "%$q%"))
                ->orWhere('pregnancy_status', 'like', "%$q%"))
            ->latest('pregnancy_confirmed_date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = Pregnancy::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $totalRecords = $baseQuery->count();
        $ongoing = (clone $baseQuery)->where('pregnancy_status', 'ongoing')->count();
        $aborted = (clone $baseQuery)->where('pregnancy_status', 'aborted')->count();
        $completed = (clone $baseQuery)->where('pregnancy_status', 'completed')->count();
        $averageGestationDays = round($baseQuery->avg('expected_gestation_days') ?? 0);

        $statistics = [
            'total_records' => $totalRecords,
            'ongoing' => $ongoing,
            'aborted' => $aborted,
            'completed' => $completed,
            'average_gestation_days' => $averageGestationDays,
        ];

        return Inertia::render('Pregnancies/Index', [
            'pregnancies' => $pregnancies,
            'filters' => $request->only('q'),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', Pregnancy::class);

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'female');
            })
            ->get();

        // Fetch reproduction records that are not yet associated with a pregnancy
        $reproductionRecords = ReproductionRecord::select('id', 'event', 'event_date', 'animal_id')
            ->whereDoesntHave('pregnancy')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($record) {
                $record->display_name = "{$record->animal->tag} - {$record->event} ({$record->event_date->format('Y-m-d')})";
                return $record;
            });

        return Inertia::render('Pregnancies/Create', [
            'animals' => $animals,
            'reproductionRecords' => $reproductionRecords,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', Pregnancy::class);

        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'reproduction_record_id' => 'required|exists:reproduction_records,id|unique:pregnancies,reproduction_record_id',
            'pregnancy_confirmed_date' => 'required|date',
            'expected_gestation_days' => 'nullable|integer|min:1',
            'pregnancy_status' => 'required|in:ongoing,confirmed,aborted,completed',
            'health_notes' => 'nullable|string',
        ]);

        $animal = Animal::findOrFail($validated['animal_id']);
        $validated['farm_id'] = $animal->farm_id;
        $validated['user_id'] = $user->id;


        // Calculate expected_calving_date
        $pregnancyConfirmedDate = Carbon::parse($validated['pregnancy_confirmed_date']);
        $expectedGestationDays = $validated['expected_gestation_days'] ?? 283;
        $validated['expected_calving_date'] = $pregnancyConfirmedDate->addDays($expectedGestationDays)->format('Y-m-d');

        DB::transaction(function () use ($validated, $user) {
            $pregnancy = Pregnancy::create($validated);

            // Automation: Create an initial pregnancy checkup record
            PregnancyCheckup::create([
                'pregnancy_id' => $pregnancy->id,
                'checkup_date' => Carbon::parse($pregnancy->pregnancy_confirmed_date)->addDays(30)->format('Y-m-d H:i:s'), // Schedule 30 days after confirmation
                'checkup_result' => CheckupResult::Normal, // Default to normal, can be updated later
                'observations' => 'Initial scheduled checkup.',
                'checked_by' => $user->id, // Assign to the current user or a designated vet
                'farm_id' => $user->farm_id
            ]);

            // Automation: Create an automatic Calving Record
            CalvingRecord::create([
                'farm_id' => $pregnancy->farm_id,
                'pregnancy_id' => $pregnancy->id,
                'calving_date' => $pregnancy->expected_calving_date,
                'calving_type' => \App\Enums\CalvingType::Normal, // Default to normal
                'calves_count' => 1, // Default to 1 calf
                'calf_gender' => \App\Enums\CalfGender::Mixed, // Default to mixed, can be updated later
                'calving_outcome' => \App\Enums\CalvingOutcome::Successful, // Default to successful, can be updated later
                'notes' => 'Automatically generated calving record based on pregnancy confirmation.',
            ]);
        });

        return redirect()->route('pregnancies.index')->with('success', 'Pregnancy record created successfully, initial checkup scheduled, and calving record automatically created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pregnancy $pregnancy)
    {
        $this->authorize('view', $pregnancy);

        $pregnancy->load(['animal', 'reproductionRecord.animal', 'farm']);

        return Inertia::render('Pregnancies/Show', [
            'pregnancy' => $pregnancy,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pregnancy $pregnancy)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $pregnancy);

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'female');
            })
            ->get();

        // Fetch reproduction records that are not yet associated with a pregnancy,
        // or the one currently associated with this pregnancy
        $reproductionRecords = ReproductionRecord::select('id', 'event', 'event_date', 'animal_id')
            ->where(function ($query) use ($pregnancy) {
                $query->whereDoesntHave('pregnancy')
                    ->orWhere('id', $pregnancy->reproduction_record_id);
            })
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($record) {
                $record->display_name = "{$record->animal->tag} - {$record->event} ({$record->event_date->format('Y-m-d')})";
                return $record;
            });

        $pregnancy->load(['animal', 'reproductionRecord']);

        return Inertia::render('Pregnancies/Edit', [
            'pregnancy' => $pregnancy,
            'animals' => $animals,
            'reproductionRecords' => $reproductionRecords,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pregnancy $pregnancy)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $pregnancy);

        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'reproduction_record_id' => 'required|exists:reproduction_records,id|unique:pregnancies,reproduction_record_id,' . $pregnancy->id,
            'pregnancy_confirmed_date' => 'required|date',
            'expected_gestation_days' => 'nullable|integer|min:1',
            'pregnancy_status' => 'required|in:ongoing,confirmed,aborted,completed',
            'health_notes' => 'nullable|string',
        ]);

        $animal = Animal::findOrFail($validated['animal_id']);
        $validated['farm_id'] = $animal->farm_id; // Ensure farm_id is consistent if animal changes

        // Recalculate expected_calving_date if relevant fields change
        if (
            $pregnancy->pregnancy_confirmed_date !== $validated['pregnancy_confirmed_date'] ||
            $pregnancy->expected_gestation_days !== ($validated['expected_gestation_days'] ?? 283)
        ) {
            $pregnancyConfirmedDate = Carbon::parse($validated['pregnancy_confirmed_date']);
            $expectedGestationDays = $validated['expected_gestation_days'] ?? 283;
            $validated['expected_calving_date'] = $pregnancyConfirmedDate->addDays($expectedGestationDays)->format('Y-m-d');
        }

        $pregnancy->update($validated);

        return redirect()->route('pregnancies.index')->with('success', 'Pregnancy record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pregnancy $pregnancy)
    {
        $this->authorize('delete', $pregnancy);

        $pregnancy->delete();

        return redirect()->route('pregnancies.index')->with('success', 'Pregnancy record deleted successfully.');
    }
}
