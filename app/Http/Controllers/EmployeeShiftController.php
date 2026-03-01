<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use App\Models\Employee;
use App\Models\Shift;
use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Http\Requests\UpdateEmployeeShiftRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeeShiftController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmployeeShift::class, 'employee_shift');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employeeShifts = EmployeeShift::query()
            ->with(['employee', 'shift'])
            ->where('user_id', Auth::id())
            ->where('farm_id', Auth::user()->farm_id)
            ->paginate(10);

        return Inertia::render('HR/EmployeeShifts/Index', [
            'employeeShifts' => $employeeShifts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::where('farm_id', Auth::user()->farm_id)->get();
        $shifts = Shift::where('farm_id', Auth::user()->farm_id)->get();

        return Inertia::render('HR/EmployeeShifts/Create', [
            'employees' => $employees,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeShiftRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['farm_id'] = Auth::user()->farm_id;
        EmployeeShift::create($validated);

        return redirect()->route('employee-shifts.index')->with('success', 'Employee shift assigned successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeShift $employeeShift)
    {
        $employeeShift->load(['employee', 'shift']);
        return Inertia::render('HR/EmployeeShifts/Show', [
            'employeeShift' => $employeeShift,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeShift $employeeShift)
    {
        $employees = Employee::where('farm_id', Auth::user()->farm_id)->get();
        $shifts = Shift::where('farm_id', Auth::user()->farm_id)->get();

        return Inertia::render('HR/EmployeeShifts/Edit', [
            'employeeShift' => $employeeShift,
            'employees' => $employees,
            'shifts' => $shifts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeShiftRequest $request, EmployeeShift $employeeShift)
    {
        $validated = $request->validated();
        $employeeShift->update($validated);

        return redirect()->route('employee-shifts.index')->with('success', 'Employee shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeShift $employeeShift)
    {
        $employeeShift->delete();

        return redirect()->route('employee-shifts.index')->with('success', 'Employee shift deleted successfully.');
    }
}
