<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDocument;
use App\Http\Requests\StoreEmployeeDocumentRequest;
use App\Http\Requests\UpdateEmployeeDocumentRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Add this import

class EmployeeDocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EmployeeDocument::class, 'employee_document');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EmployeeDocument::with('employee');

        /** @var \App\Models\User $user */ // Add this docblock
        $user = Auth::user(); // Use Auth facade

        if ($user->hasRole('farm owner')) {
            $query->where('farm_id', $user->farm_id);
        }

        // Apply search filter if 'q' is present in the request
        if ($request->has('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('document_number', 'like', '%' . $request->q . '%')
                    ->orWhere('document_type', 'like', '%' . $request->q . '%');
            });
        }

        $employeeDocuments = $query->paginate(10)->withQueryString();

        return Inertia::render('HR/EmployeeDocuments/Index', [
            'employeeDocuments' => $employeeDocuments,
            'filters' => $request->only(['q']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employees = Employee::where('farm_id', $user->farm_id)->get();
        return Inertia::render('HR/EmployeeDocuments/Create', [
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeDocumentRequest $request)
    {
        $validated = $request->validated();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $validated['user_id'] = $user->id;
        $validated['farm_id'] = $user->farm_id;

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('employee_documents', 'public');
        }

        EmployeeDocument::create($validated);

        return redirect()->route('employee-documents.index')->with('success', 'Employee document created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeDocument $employeeDocument)
    {
        return Inertia::render('HR/EmployeeDocuments/Show', [
            'employeeDocument' => $employeeDocument->load('employee'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeDocument $employeeDocument)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $employees = Employee::where('farm_id', $user->farm_id)->get();
        return Inertia::render('HR/EmployeeDocuments/Edit', [
            'employeeDocument' => $employeeDocument->load('employee'),
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeDocumentRequest $request, EmployeeDocument $employeeDocument)
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($employeeDocument->file_path) {
                Storage::disk('public')->delete($employeeDocument->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('employee_documents', 'public');
        }

        $employeeDocument->update($validated);

        return redirect()->route('employee-documents.index')->with('success', 'Employee document updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeDocument $employeeDocument)
    {
        if ($employeeDocument->file_path) {
            Storage::disk('public')->delete($employeeDocument->file_path);
        }
        $employeeDocument->delete();
        return redirect()->route('employee-documents.index')->with('success', 'Employee document deleted successfully.');
    }
}
