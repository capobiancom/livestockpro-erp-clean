<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Farm;
use App\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Expense::class, 'expense');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $expenses = Expense::with(['farm', 'staff'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('title', 'like', "%$q%")
                ->orWhere('notes', 'like', "%$q%"))
            ->latest('incurred_on')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = Expense::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_expenses' => (clone $baseQuery)->count(),
            'total_amount' => (clone $baseQuery)->sum('amount'),
            'this_month' => (clone $baseQuery)->whereMonth('incurred_on', now()->month)
                ->whereYear('incurred_on', now()->year)
                ->sum('amount'),
            'this_year' => (clone $baseQuery)->whereYear('incurred_on', now()->year)
                ->sum('amount'),
        ];

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Expenses/Create', [
            'farms' => $farms,
            'staff' => $staff,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'incurred_on' => 'required|date',
            'farm_id' => 'nullable|exists:farms,id',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
            $validated['user_id'] = $user->id;
        }
        // Validate that the selected staff belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['staff_id'])) {
            $staffProfile = StaffProfile::where('id', $validated['staff_id'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['staff_id' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['farm', 'staff']);
        return Inertia::render('Expenses/Show', ['expense' => $expense]);
    }

    public function edit(Expense $expense)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();
        $staff = StaffProfile::select('id', 'first_name', 'last_name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'farms' => $farms,
            'staff' => $staff,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'incurred_on' => 'required|date',
            'farm_id' => 'nullable|exists:farms,id',
            'staff_id' => 'nullable|exists:staff_profiles,id',
            'notes' => 'nullable|string',
        ]);

        // Enforce farm_id for farm owners
        if ($user->hasRole('farm owner')) {
            $validated['farm_id'] = $user->farm_id;
        }
        // Validate that the selected staff belongs to the user's farm
        if ($user->hasRole('farm owner') && !empty($validated['staff_id'])) {
            $staffProfile = StaffProfile::where('id', $validated['staff_id'])
                ->where('farm_id', $user->farm_id)
                ->first();
            if (!$staffProfile) {
                return back()->withErrors(['staff_id' => 'The selected staff member does not belong to your farm.'])->withInput();
            }
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
