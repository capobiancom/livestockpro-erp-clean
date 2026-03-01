<?php

namespace App\Http\Controllers;

use App\Models\VaccineType;
use App\Models\Farm; // Import Farm model
use App\Data\VaccineTypeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class VaccineTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(VaccineType::class, 'vaccine_type');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $types = VaccineType::withCount('vaccinations')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%")
                ->orWhere('manufacturer', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = VaccineType::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_types' => (clone $baseQuery)->count(),
            'with_manufacturer' => (clone $baseQuery)->whereNotNull('manufacturer')->count(),
            'total_vaccinations' => \App\Models\VaccinationRecord::when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->count(),
            'unique_manufacturers' => (clone $baseQuery)->whereNotNull('manufacturer')
                ->distinct('manufacturer')
                ->count('manufacturer'),
        ];

        return Inertia::render('VaccineTypes/Index', [
            'vaccineTypes' => $types,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        return Inertia::render('VaccineTypes/Create', ['farms' => $farms]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'manufacturer' => 'nullable|string',
            'dose' => 'nullable|string',
            'doses_per_animal' => 'nullable|integer',
            'route' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }
        $data = VaccineTypeData::from($validated);

        VaccineType::create($data->toArray());

        return redirect()->route('vaccine-types.index')->with('success', 'Vaccine type created.');
    }

    public function show(VaccineType $vaccineType)
    {
        $vaccineType->loadCount('vaccinations');
        return Inertia::render('VaccineTypes/Show', ['vaccineType' => $vaccineType]);
    }

    public function edit(VaccineType $vaccineType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        return Inertia::render('VaccineTypes/Edit', [
            'vaccineType' => $vaccineType,
            'farms' => $farms,
        ]);
    }

    public function update(Request $request, VaccineType $vaccineType)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => "required|string|unique:vaccine_types,name,{$vaccineType->id}",
            'manufacturer' => 'nullable|string',
            'dose' => 'nullable|string',
            'doses_per_animal' => 'nullable|integer',
            'route' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $data = VaccineTypeData::from($validated);
        $vaccineType->update($data->toArray());

        return redirect()->route('vaccine-types.index')->with('success', 'Vaccine type updated.');
    }

    public function destroy(VaccineType $vaccineType)
    {
        $vaccineType->delete();
        return redirect()->route('vaccine-types.index')->with('success', 'Vaccine type removed.');
    }
}
