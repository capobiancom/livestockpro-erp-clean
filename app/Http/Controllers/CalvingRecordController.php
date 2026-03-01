<?php

namespace App\Http\Controllers;

use App\Enums\CalfGender;
use App\Enums\CalvingOutcome;
use App\Enums\CalvingType;
use App\Enums\HealthStatus;
use App\Models\Calf;
use App\Models\CalvingRecord;
use App\Models\Pregnancy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Enums\PregnancyStatus; // Import PregnancyStatus enum

class CalvingRecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CalvingRecord::class, 'calving_record');
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
        $baseQuery = CalvingRecord::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $totalRecords = $baseQuery->count();
        $normalCalvings = (clone $baseQuery)->where('calving_type', CalvingType::Normal->value)->count();
        $assistedCalvings = (clone $baseQuery)->where('calving_type', CalvingType::Assisted->value)->count();
        $cSectionCalvings = (clone $baseQuery)->where('calving_type', CalvingType::CSection->value)->count();
        $successfulOutcomes = (clone $baseQuery)->where('calving_outcome', CalvingOutcome::Successful->value)->count();
        $stillbirthOutcomes = (clone $baseQuery)->where('calving_outcome', CalvingOutcome::Stillbirth->value)->count();
        $complicationOutcomes = (clone $baseQuery)->where('calving_outcome', CalvingOutcome::Complication->value)->count();


        $calvingRecords = $baseQuery->with(['pregnancy.animal'])
            ->when($q, fn($qb) => $qb->whereHas('pregnancy.animal', fn($aq) => $aq->where('tag', 'like', "%$q%")->orWhere('name', 'like', "%$q%"))
                ->orWhere('calving_type', 'like', "%$q%")
                ->orWhere('calving_outcome', 'like', "%$q%"))
            ->latest('calving_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('CalvingRecords/Index', [
            'calvingRecords' => $calvingRecords,
            'filters' => $request->only('q'),
            'statistics' => [
                'total_records' => $totalRecords,
                'normal_calvings' => $normalCalvings,
                'assisted_calvings' => $assistedCalvings,
                'c_section_calvings' => $cSectionCalvings,
                'successful_outcomes' => $successfulOutcomes,
                'stillbirth_outcomes' => $stillbirthOutcomes,
                'complication_outcomes' => $complicationOutcomes,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', CalvingRecord::class);

        $pregnancies = Pregnancy::select('id', 'animal_id', 'pregnancy_confirmed_date')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($pregnancy) {
                $pregnancy->display_name = "{$pregnancy->animal->tag} - Confirmed: {$pregnancy->pregnancy_confirmed_date->format('Y-m-d')}";
                return $pregnancy;
            });

        $calvingTypes = CalvingType::cases();
        $calfGenders = CalfGender::cases();
        $calvingOutcomes = CalvingOutcome::cases();

        return Inertia::render('CalvingRecords/Create', [
            'pregnancies' => $pregnancies,
            'calvingTypes' => $calvingTypes,
            'calfGenders' => $calfGenders,
            'calvingOutcomes' => $calvingOutcomes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\CalvingRecordRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('create', CalvingRecord::class);

        $validated = $request->validated();
        $validated['farm_id'] = $user->farm_id;
        $validated['user_id'] = $user->id;

        $calvingRecord = CalvingRecord::create($validated);

        // Automation: Update pregnancy status to completed
        $pregnancy = Pregnancy::find($validated['pregnancy_id']);
        if ($pregnancy) {
            $pregnancy->update(['pregnancy_status' => PregnancyStatus::Completed->value]);
        }

        // Create a new Calf record
        if ($validated['calving_outcome'] === CalvingOutcome::Successful->value) {
            // Load the pregnancy with its reproduction record to get the sire_id
            $pregnancy->load('reproductionRecord');
            $fatherId = $pregnancy->reproductionRecord->partner_id ?? null;

            // Load the mother animal to get its tag for generating calf tag numbers
            $motherAnimal = $calvingRecord->pregnancy->animal;

            for ($i = 0; $i < $validated['calves_count']; $i++) {
                Calf::create([
                    'farm_id' => $user->farm_id,
                    'mother_id' => $motherAnimal->id,
                    'father_id' => $fatherId,
                    'tag_number' => $motherAnimal->tag . '-C' . ($i + 1) . '-' . \Illuminate\Support\Str::random(4), // Generate a unique tag number based on mother's tag
                    'gender' => $validated['calf_gender'],
                    'birth_date' => $validated['calving_date'],
                    'birth_weight' => null, // This can be added to the form later if needed
                    'health_status' => HealthStatus::Healthy,
                ]);
            }
        }

        return redirect()->route('calving-records.index')->with('success', 'Calving record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalvingRecord $calvingRecord)
    {
        $this->authorize('view', $calvingRecord);

        $calvingRecord->load(['pregnancy.animal']);

        return Inertia::render('CalvingRecords/Show', [
            'calvingRecord' => $calvingRecord,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalvingRecord $calvingRecord)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $calvingRecord);

        $pregnancies = Pregnancy::select('id', 'animal_id', 'pregnancy_confirmed_date')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->with('animal:id,tag,name')
            ->get()
            ->map(function ($pregnancy) {
                $pregnancy->display_name = "{$pregnancy->animal->tag} - Confirmed: {$pregnancy->pregnancy_confirmed_date->format('Y-m-d')}";
                return $pregnancy;
            });

        $calvingTypes = CalvingType::cases();
        $calfGenders = CalfGender::cases();
        $calvingOutcomes = CalvingOutcome::cases();

        return Inertia::render('CalvingRecords/Edit', [
            'calvingRecord' => $calvingRecord,
            'pregnancies' => $pregnancies,
            'calvingTypes' => $calvingTypes,
            'calfGenders' => $calfGenders,
            'calvingOutcomes' => $calvingOutcomes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\CalvingRecordRequest $request, CalvingRecord $calvingRecord)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $calvingRecord);

        $validated = $request->validated();
        $validated['farm_id'] = $user->farm_id;

        $calvingRecord->update($validated);

        return redirect()->route('calving-records.index')->with('success', 'Calving record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalvingRecord $calvingRecord)
    {
        $this->authorize('delete', $calvingRecord);

        $calvingRecord->delete();

        return redirect()->route('calving-records.index')->with('success', 'Calving record deleted successfully.');
    }
}
