<?php

namespace App\Http\Controllers;

use App\Models\Logistic;
use App\Models\Farm; // Import Farm model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class LogisticController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Logistic::class, 'logistic');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $logistics = Logistic::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })
            ->when($q, fn($qb) => $qb->where('reference', 'like', "%$q%")
                ->orWhere('from_location', 'like', "%$q%")
                ->orWhere('to_location', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = Logistic::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_trips' => (clone $baseQuery)->count(),
            'total_animals_moved' => (clone $baseQuery)->sum('animals_count'),
            'completed_trips' => (clone $baseQuery)->whereNotNull('arrival_at')->count(),
            'total_cost' => (clone $baseQuery)->sum('cost'),
        ];

        return Inertia::render('Logistics/Index', [
            'logistics' => $logistics,
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
        return Inertia::render('Logistics/Create', ['farms' => $farms]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100',
            'vehicle' => 'nullable|string|max:100',
            'driver' => 'nullable|string|max:100',
            'purpose' => 'required|string|max:255',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_at' => 'required|date',
            'arrival_at' => 'nullable|date',
            'animals_count' => 'nullable|integer|min:0',
            'animal_ids' => 'nullable|array',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        Logistic::create($validated);

        return redirect()->route('logistics.index')->with('success', 'Logistics record created successfully.');
    }

    public function show(Logistic $logistic)
    {
        return Inertia::render('Logistics/Show', ['logistic' => $logistic]);
    }

    public function edit(Logistic $logistic)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        return Inertia::render('Logistics/Edit', ['logistic' => $logistic, 'farms' => $farms]);
    }

    public function update(Request $request, Logistic $logistic)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'reference' => 'nullable|string|max:100',
            'vehicle' => 'nullable|string|max:100',
            'driver' => 'nullable|string|max:100',
            'purpose' => 'required|string|max:255',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_at' => 'required|date',
            'arrival_at' => 'nullable|date',
            'animals_count' => 'nullable|integer|min:0',
            'animal_ids' => 'nullable|array',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $logistic->update($validated);

        return redirect()->route('logistics.index')->with('success', 'Logistics record updated successfully.');
    }

    public function destroy(Logistic $logistic)
    {
        $logistic->delete();
        return redirect()->route('logistics.index')->with('success', 'Logistics record deleted successfully.');
    }
}
