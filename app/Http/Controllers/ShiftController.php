<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Shift::class, 'shift');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::query()
            ->where('user_id', Auth::id())
            ->paginate(10);

        return Inertia::render('HR/Shifts/Index', [
            'shifts' => $shifts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('HR/Shifts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['farm_id'] = Auth::user()->farm_id;

        Shift::create($validated);

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        return Inertia::render('HR/Shifts/Show', [
            'shift' => $shift,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        return Inertia::render('HR/Shifts/Edit', [
            'shift' => $shift,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, Shift $shift)
    {
        $validated = $request->validated();
        $shift->update($validated);

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift deleted successfully.');
    }
}
