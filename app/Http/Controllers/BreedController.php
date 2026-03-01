<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use App\Data\BreedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class BreedController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Breed::class, 'breed');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $breeds = Breed::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Breeds/Index', [
            'breeds' => $breeds,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        return Inertia::render('Breeds/Create');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => 'required|string|unique:breeds,name,NULL,id,farm_id,' . $user->farm_id,
            'code' => 'nullable|string',
            'description' => 'nullable|string',
            'characteristics' => 'nullable|array',
            'origin' => 'required|in:local,exotic,cross',
            'animal_type' => 'required|in:cow,bull',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        $data = BreedData::from($validated);
        Breed::create($data->toArray());

        return redirect()->route('breeds.index')->with('success', 'Breed created.');
    }

    public function show(Breed $breed)
    {
        return Inertia::render('Breeds/Show', ['breed' => $breed]);
    }

    public function edit(Breed $breed)
    {
        return Inertia::render('Breeds/Edit', ['breed' => $breed]);
    }

    public function update(Request $request, Breed $breed)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'name' => "required|string|unique:breeds,name,{$breed->id},id,farm_id," . $user->farm_id,
            'code' => 'nullable|string',
            'description' => 'nullable|string',
            'characteristics' => 'nullable|array',
            'origin' => 'required|in:local,exotic,cross',
            'animal_type' => 'required|in:cow,bull',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }

        $data = BreedData::from($validated);
        $breed->update($data->toArray());

        return redirect()->route('breeds.index')->with('success', 'Breed updated.');
    }

    public function destroy(Breed $breed)
    {
        $breed->delete();
        return redirect()->route('breeds.index')->with('success', 'Breed removed.');
    }
}
