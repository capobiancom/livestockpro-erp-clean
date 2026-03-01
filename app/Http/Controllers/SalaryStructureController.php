<?php

namespace App\Http\Controllers;

use App\Models\SalaryStructure;
use App\Models\Employee;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\StoreSalaryStructureRequest;
use App\Http\Requests\UpdateSalaryStructureRequest;

class SalaryStructureController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SalaryStructure::class, 'salary_structure');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');

        $salaryStructures = SalaryStructure::with(['employee', 'farm', 'user'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('employee', function ($query) use ($q) {
                $query->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%");
            }))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('SalaryStructures/Index', [
            'salaryStructures' => $salaryStructures,
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        return Inertia::render('SalaryStructures/Create', [
            'employees' => $employees,
            'farms' => $farms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryStructureRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $validated['farm_id'] = $user->farm_id;
        $validated['user_id'] = $user->id;

        SalaryStructure::create($validated);

        return redirect()->route('salary-structures.index')->with('success', 'Salary structure created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalaryStructure $salaryStructure)
    {
        $salaryStructure->load(['employee', 'farm', 'user']);
        return Inertia::render('SalaryStructures/Show', ['salaryStructure' => $salaryStructure]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryStructure $salaryStructure)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        $salaryStructure->load(['employee', 'farm', 'user']);

        return Inertia::render('SalaryStructures/Edit', [
            'salaryStructure' => $salaryStructure,
            'employees' => $employees,
            'farms' => $farms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalaryStructureRequest $request, SalaryStructure $salaryStructure)
    {
        $validated = $request->validated();
        $salaryStructure->update($validated);

        return redirect()->route('salary-structures.show', $salaryStructure)->with('success', 'Salary structure updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryStructure $salaryStructure)
    {
        $salaryStructure->delete();
        return redirect()->route('salary-structures.index')->with('success', 'Salary structure deleted successfully!');
    }
}
