<?php

namespace App\Http\Controllers;

use App\Models\MedicineGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MedicineGroupController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MedicineGroup::class, 'medicine_group');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $medicineGroups = MedicineGroup::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('MedicineGroups/Index', [
            'medicineGroups' => $medicineGroups,
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('MedicineGroups/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MedicineGroup::create([
            'farm_id' => $user->farm_id,
            'user_id' => $user->id,
            'name' => $validated['name'],
        ]);

        return redirect()->route('medicine-groups.index')->with('success', 'Medicine group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicineGroup $medicineGroup)
    {
        $medicineGroup->load('farm');
        return Inertia::render('MedicineGroups/Show', ['medicineGroup' => $medicineGroup]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicineGroup $medicineGroup)
    {
        return Inertia::render('MedicineGroups/Edit', ['medicineGroup' => $medicineGroup]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicineGroup $medicineGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $medicineGroup->update($validated);

        return redirect()->route('medicine-groups.index')->with('success', 'Medicine group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicineGroup $medicineGroup)
    {
        $medicineGroup->delete();
        return redirect()->route('medicine-groups.index')->with('success', 'Medicine group deleted successfully.');
    }
}
