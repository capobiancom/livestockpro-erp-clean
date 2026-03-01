<?php

namespace App\Http\Controllers;

use App\Models\MilkRecord;
use App\Models\Animal;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class MilkRecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MilkRecord::class, 'milk_record');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $records = MilkRecord::with(['animal', 'staff'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('animal', fn($aq) => $aq->where('tag', 'like', "%$q%")))
            ->latest('date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = MilkRecord::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_records' => (clone $baseQuery)->count(),
            'total_production' => (clone $baseQuery)->sum('quantity_liters'),
            'today_production' => (clone $baseQuery)->whereDate('date', today())->sum('quantity_liters'),
            'this_month' => (clone $baseQuery)->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('quantity_liters'),
        ];

        return Inertia::render('MilkRecords/Index', [
            'records' => $records,
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
        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('MilkRecords/Create', [
            'animals' => $animals,
            'staff' => $staff,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'date' => 'required|date',
            'quantity_liters' => 'required|numeric|min:0',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        MilkRecord::create($validated);

        return redirect()->route('milk-records.index')->with('success', 'Milk record created successfully.');
    }

    public function show(MilkRecord $milkRecord)
    {
        $this->authorize('view', $milkRecord);
        $milkRecord->load(['animal', 'staff']);
        return Inertia::render('MilkRecords/Show', ['record' => $milkRecord]);
    }

    public function edit(MilkRecord $milkRecord)
    {
        $this->authorize('update', $milkRecord);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $animals = Animal::select('id', 'tag', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('MilkRecords/Edit', [
            'record' => array_merge($milkRecord->toArray(), [
                'date' => $milkRecord->date->format('Y-m-d'),
            ]),
            'animals' => $animals,
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, MilkRecord $milkRecord)
    {
        $this->authorize('update', $milkRecord);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'date' => 'required|date',
            'quantity_liters' => 'required|numeric|min:0',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $animal = Animal::find($validated['animal_id']);
            if ($animal && $animal->farm_id != $user->farm_id) {
                return back()->withErrors(['animal_id' => 'The selected animal does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
        }

        $milkRecord->update($validated);

        return redirect()->route('milk-records.index')->with('success', 'Milk record updated successfully.');
    }

    public function destroy(MilkRecord $milkRecord)
    {
        $this->authorize('delete', $milkRecord);
        $milkRecord->delete();
        return redirect()->route('milk-records.index')->with('success', 'Milk record deleted successfully.');
    }
}
