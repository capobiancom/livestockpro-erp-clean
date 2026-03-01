<?php

namespace App\Http\Controllers;

use App\Models\Herd;
use App\Models\Farm;
use App\Data\HerdData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class HerdController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Herd::class, 'herd');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $herds = Herd::with('farm')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();
        return Inertia::render('Herds/Index', [
            'herds' => $herds,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        return Inertia::render('Herds/Create', ['farms' => $farms]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string',
            'code' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $farm = Farm::find($validated['farm_id']);
            if ($farm && $farm->id != $user->farm_id) {
                return back()->withErrors(['farm_id' => 'The selected farm does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }

        $data = HerdData::from($validated);
        Herd::create($data->toArray());

        return redirect()->route('herds.index')->with('success', 'Herd created.');
    }

    public function show(Herd $herd)
    {
        $herd->load('farm');
        return Inertia::render('Herds/Show', ['herd' => $herd]);
    }

    public function edit(Herd $herd)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        return Inertia::render('Herds/Edit', ['herd' => $herd, 'farms' => $farms]);
    }

    public function update(Request $request, Herd $herd)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'name' => 'required|string',
            'code' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $farm = Farm::find($validated['farm_id']);
            if ($farm && $farm->id != $user->farm_id) {
                return back()->withErrors(['farm_id' => 'The selected farm does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
        }

        $data = HerdData::from($validated);
        $herd->update($data->toArray());

        return redirect()->route('herds.index')->with('success', 'Herd updated.');
    }

    public function destroy(Herd $herd)
    {
        $herd->delete();
        return redirect()->route('herds.index')->with('success', 'Herd removed.');
    }
}
