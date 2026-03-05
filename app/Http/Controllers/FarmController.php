<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Data\FarmData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FarmController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Farm::class, 'farm');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = $request->input('q');

        $farms = Farm::withCount(['animals', 'staff', 'herds'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                // Farm owners can manage multiple farms; list farms they own.
                $query->where('user_id', $user->id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Farms/Index', [
            'farms' => $farms,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Farms/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'nullable|string|unique:farms,code',
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_phone' => 'nullable|string',
        ]);

        $data = FarmData::from($validated);

        // Ensure the creating user becomes the owner of the farm.
        $payload = array_merge($data->toArray(), [
            'user_id' => $request->user()->id,
        ]);

        Farm::create($payload);

        return redirect()->route('farms.index')->with('success', 'Farm created');
    }

    public function show(Farm $farm)
    {
        $farm->loadCount(['animals', 'staff', 'herds']);
        return Inertia::render('Farms/Show', ['farm' => $farm]);
    }

    public function edit(Farm $farm)
    {
        return Inertia::render('Farms/Edit', ['farm' => $farm]);
    }

    public function update(Request $request, Farm $farm)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => "nullable|string|unique:farms,code,{$farm->id}",
            'address' => 'nullable|string',
            'contact_name' => 'nullable|string',
            'contact_phone' => 'nullable|string',
        ]);

        $data = FarmData::from($validated);
        $farm->update($data->toArray());
        return redirect()->route('farms.index')->with('success', 'Farm updated');
    }

    public function destroy(Farm $farm)
    {
        $farm->delete();
        return redirect()->route('farms.index')->with('success', 'Farm removed');
    }
}
