<?php

namespace App\Http\Controllers;

use App\Enums\CalfGender;
use App\Enums\HealthStatus;
use App\Models\Animal;
use App\Models\Calf;
use App\Models\User;
use App\Enums\AnimalType;
use App\Enums\AnimalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CalfController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Calf::class, 'calf');
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
        $baseQuery = Calf::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $totalCalves = $baseQuery->count();
        $healthyCalves = (clone $baseQuery)->where('health_status', HealthStatus::Healthy->value)->count();
        $weakCalves = (clone $baseQuery)->where('health_status', HealthStatus::Weak->value)->count();
        $criticalCalves = (clone $baseQuery)->where('health_status', HealthStatus::Critical->value)->count();
        $maleCalves = (clone $baseQuery)->where('gender', CalfGender::Male->value)->count();
        $femaleCalves = (clone $baseQuery)->where('gender', CalfGender::Female->value)->count();


        $calves = $baseQuery->with(['mother:id,tag,name', 'father:id,tag,name'])
            ->when($q, function ($query, $q) {
                $query->where('tag_number', 'like', "%$q%")
                    ->orWhereHas('mother', fn($q_mother) => $q_mother->where('tag', 'like', "%$q%")->orWhere('name', 'like', "%$q%"))
                    ->orWhereHas('father', fn($q_father) => $q_father->where('tag', 'like', "%$q%")->orWhere('name', 'like', "%$q%"))
                    ->orWhere('health_status', 'like', "%$q%");
            })
            ->latest('birth_date')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Calves/Index', [
            'calves' => $calves,
            'filters' => $request->only('q'),
            'statistics' => [
                'total_calves' => $totalCalves,
                'healthy_calves' => $healthyCalves,
                'weak_calves' => $weakCalves,
                'critical_calves' => $criticalCalves,
                'male_calves' => $maleCalves,
                'female_calves' => $femaleCalves,
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

        $this->authorize('create', Calf::class);

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'female');
            })
            ->get();

        $fanimals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'male');
            })
            ->get();

        $calfGenders = CalfGender::cases();
        $healthStatuses = HealthStatus::cases();

        return Inertia::render('Calves/Create', [
            'animals' => $animals,
            'fanimals' => $fanimals,
            'calfGenders' => $calfGenders,
            'healthStatuses' => $healthStatuses,
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

        $this->authorize('create', Calf::class);

        $validated = $request->validate([
            'mother_id' => ['required', 'exists:animals,id'],
            'father_id' => ['nullable', 'exists:animals,id'],
            'gender' => ['required', Rule::enum(CalfGender::class)],
            'birth_date' => ['required', 'date'],
            'birth_weight' => ['nullable', 'numeric', 'min:0'],
            'health_status' => ['required', Rule::enum(HealthStatus::class)],
        ]);

        $validated['farm_id'] = $user->farm_id;
        $validated['user_id'] = $user->id;
        $validated['tag_number'] = $this->generateUniqueTagNumber(); // Automatically generate tag number

        $calf = Calf::create($validated);

        // Automatically update animal inventory
        $mother = Animal::find($validated['mother_id']);
        if ($mother) {
            Animal::create([
                'tag' => $calf->tag_number,
                'name' => 'Calf ' . $calf->tag_number, // Default name for the calf
                'animal_type' => AnimalType::Calf->value, // Assuming 'Calf' is a valid animal type
                'sex' => $calf->gender->value,
                'dob' => $calf->birth_date,
                'breed_id' => $mother->breed_id, // Inherit breed from mother
                'farm_id' => $user->farm_id,
                'herd_id' => $mother->herd_id, // Inherit herd from mother
                'status' => AnimalStatus::Active->value, // Assuming 'Active' is a valid animal status
                'current_weight_kg' => $calf->birth_weight,
                'acquired_at' => $calf->birth_date,
                'source' => 'Born on Farm',
                'notes' => 'Born from mother ' . $mother->tag . ' (' . $mother->name . ')',
            ]);
        }


        return redirect()->route('calves.index')->with('success', 'Calf created successfully.');
    }

    /**
     * Generate a unique tag number for a new calf.
     */
    private function generateUniqueTagNumber(): string
    {
        return strtoupper(fake()->bothify('TAG-####') . '-' . uniqid());
    }

    /**
     * Display the specified resource.
     */
    public function show(Calf $calf)
    {
        $this->authorize('view', $calf);

        $calf->load(['mother:id,tag,name', 'father:id,tag,name']);

        return Inertia::render('Calves/Show', [
            'calf' => $calf,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calf $calf)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $calf);

        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'female');
            })
            ->get();

        $fanimals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id)->where('sex', 'male');
            })
            ->get();

        $calfGenders = CalfGender::cases();
        $healthStatuses = HealthStatus::cases();

        // Format birth_date to 'YYYY-MM-DD' for the HTML date input
        $calf->birth_date = $calf->birth_date->format('Y-m-d');

        return Inertia::render('Calves/Edit', [
            'calf' => $calf,
            'animals' => $animals,
            'fanimals' => $fanimals,
            'calfGenders' => $calfGenders,
            'healthStatuses' => $healthStatuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Calf $calf)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $this->authorize('update', $calf);

        $validated = $request->validate([
            'tag_number' => ['required', 'string', 'max:255', Rule::unique('calves')->ignore($calf->id)->where('farm_id', $user->farm_id)],
            'mother_id' => ['required', 'exists:animals,id'],
            'father_id' => ['nullable', 'exists:animals,id'],
            'gender' => ['required', Rule::enum(CalfGender::class)],
            'birth_date' => ['required', 'date'],
            'birth_weight' => ['nullable', 'numeric', 'min:0'],
            'health_status' => ['required', Rule::enum(HealthStatus::class)],
        ]);

        $calf->update($validated);

        return redirect()->route('calves.index')->with('success', 'Calf updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calf $calf)
    {
        $this->authorize('delete', $calf);

        $calf->delete();

        return redirect()->route('calves.index')->with('success', 'Calf deleted successfully.');
    }
}
