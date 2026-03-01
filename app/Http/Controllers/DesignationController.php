<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Designation::class, 'designation');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Designation::where('farm_id', Auth::user()->farm_id)
            ->with('farm');

        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        $designations = $query->paginate(10);

        return Inertia::render('HR/Designations/Index', [
            'designations' => $designations,
            'filters' => $request->only(['q']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('HR/Designations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request)
    {
        $data = $request->validated();

        Designation::create([
            'name' => $data['name'],
            'level' => $data['level'],
            'farm_id' => Auth::user()->farm_id,
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Designation $designation)
    {
        return Inertia::render('HR/Designations/Show', [
            'designation' => $designation->load('farm'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Designation $designation)
    {
        return Inertia::render('HR/Designations/Edit', [
            'designation' => $designation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignationRequest $request, Designation $designation)
    {
        $data = $request->validated();

        $designation->update([
            'name' => $data['name'],
            'level' => $data['level']
        ]);

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designation $designation)
    {
        $designation->delete();
        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }
}
