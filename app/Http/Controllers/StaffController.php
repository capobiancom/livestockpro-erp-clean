<?php

namespace App\Http\Controllers;

use App\Models\StaffProfile;
use App\Models\Farm;
use App\Data\StaffProfileData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StaffProfile::class, 'staff');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $staff = StaffProfile::with('farm')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('first_name', 'like', "%$q%")
                ->orWhere('last_name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();
        return Inertia::render('Staff/Index', [
            'staff' => $staff,
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
        return Inertia::render('Staff/Create', ['farms' => $farms]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'position' => 'nullable|string',
            'farm_id' => 'nullable|exists:farms,id',
            'hired_at' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'notes' => 'nullable|string',
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

        $metadata = [];
        if (isset($validated['salary'])) {
            $metadata['salary'] = $validated['salary'];
            unset($validated['salary']);
        }
        if (isset($validated['notes'])) {
            $metadata['notes'] = $validated['notes'];
            unset($validated['notes']);
        }
        if (!empty($metadata)) {
            $validated['metadata'] = $metadata;
        }

        $data = StaffProfileData::fromValidated($validated);
        StaffProfile::create($data->toArray());
        return redirect()->route('staff.index')->with('success', 'Staff created');
    }

    public function show(StaffProfile $staff)
    {
        $this->authorize('view', $staff);
        $staff->load('farm');
        return Inertia::render('Staff/Show', ['staff' => $staff]);
    }

    public function edit(StaffProfile $staff)
    {
        $this->authorize('update', $staff);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $staff->load('farm');
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        return Inertia::render('Staff/Edit', [
            'staff' => $staff,
            'farms' => $farms,
        ]);
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $this->authorize('update', $staff);
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'position' => 'nullable|string',
            'farm_id' => 'nullable|exists:farms,id',
            'hired_at' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $farm = Farm::find($validated['farm_id']);
            if ($farm && $farm->id != $user->farm_id) {
                return back()->withErrors(['farm_id' => 'The selected farm does not belong to your farm.'])->withInput();
            }
            $validated['farm_id'] = $user->farm_id;
        }

        $metadata = [];
        if (isset($validated['salary'])) {
            $metadata['salary'] = $validated['salary'];
            unset($validated['salary']);
        }
        if (isset($validated['notes'])) {
            $metadata['notes'] = $validated['notes'];
            unset($validated['notes']);
        }
        if (!empty($metadata)) {
            $validated['metadata'] = $metadata;
        }

        $data = StaffProfileData::fromValidated($validated);
        $staff->update($data->toArray());

        return redirect()->route('staff.index')->with('success', 'Staff updated');
    }

    public function destroy(StaffProfile $staff)
    {
        $this->authorize('delete', $staff);
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted');
    }
}
