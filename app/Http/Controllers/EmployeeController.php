<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Employee::class, 'employee');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['q']);
        $employees = Employee::query()
            ->with(['department', 'designation', 'user'])
            ->when($request->input('q'), function ($query, $q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('employee_code', 'like', "%{$q}%")
                    ->orWhereHas('department', function ($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('designation', function ($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%");
                    });
            })
            ->where('farm_id', Auth::user()->farm_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('HR/Employees/Index', [
            'employees' => $employees,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $departments = Department::where('farm_id', Auth::user()->farm_id)->get();
        $designations = Designation::where('farm_id', Auth::user()->farm_id)->get();

        return Inertia::render('HR/Employees/Create', [
            'departments' => $departments,
            'designations' => $designations,
            'farm_id' => Auth::user()->farm_id,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Create user credentials
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['user_email'],
            'password' => Hash::make($validated['password']),
            'farm_id' => $validated['farm_id'],
        ]);

        // Create employee and associate with the newly created user
        $validated['employee_code'] = 'EMP' . str_pad(Employee::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $employee = Employee::create(array_merge($validated, ['employee_user_id' => Auth::id()]));

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee): Response
    {
        $employee->load(['department', 'designation', 'user']);
        return Inertia::render('HR/Employees/Show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee): Response
    {
        $departments = Department::where('farm_id', Auth::user()->farm_id)->get();
        $designations = Designation::where('farm_id', Auth::user()->farm_id)->get();

        return Inertia::render('HR/Employees/Edit', [
            'employee' => $employee->load(['department', 'designation', 'user']),
            'departments' => $departments,
            'designations' => $designations,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validated(); // Exclude email and password from employee update

        //Update user credentials if email or password is provided
        $user = $employee->user;

        if (!$user) {

            $user = User::create([
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'email' => $validated['user_email'],
                'password' => Hash::make($validated['password']),
                'farm_id' => $validated['farm_id'],
            ]);
        }

        if ($user && $request->user_email !== $employee->email && $request->password !== '') {
            $userData = [];
            if (isset($validated['user_email']) && $validated['user_email'] !== $user->email) {
                $userData['email'] = $validated['user_email'];
            }
            if (isset($validated['password']) && !empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }
            if (!empty($userData)) {

                $user->update($userData);
            }
        }

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->user()->delete(); // Delete associated user
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
