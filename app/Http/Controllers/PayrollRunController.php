<?php

namespace App\Http\Controllers;

use App\Models\PayrollRun;
use App\Models\Farm;
use App\Models\User;
use App\Data\PayrollRunData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PayrollRunController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PayrollRun::class, 'payroll_run');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');

        $payrollRuns = PayrollRun::with(['farm', 'user'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('month', 'like', "%$q%")
                ->orWhere('year', 'like', "%$q%"))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('PayrollRuns/Index', [
            'payrollRuns' => $payrollRuns,
            'filters' => $request->only('q'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $users = User::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        return Inertia::render('PayrollRuns/Create', [
            'farms' => $farms,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2100',
            'status' => 'required|in:draft,finalized,paid',
            'generated_at' => 'nullable|date',
        ]);

        $validated['farm_id'] = $user->farm_id;
        $validated['user_id'] = $user->id;

        $data = PayrollRunData::from($validated);
        PayrollRun::create($data->toArray());

        return redirect()->route('payroll-runs.index')->with('success', 'Payroll Run created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PayrollRun $payrollRun)
    {
        $payrollRun->load(['farm', 'user']);
        return Inertia::render('PayrollRuns/Show', ['payrollRun' => $payrollRun]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PayrollRun $payrollRun)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $users = User::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        return Inertia::render('PayrollRuns/Edit', [
            'payrollRun' => $payrollRun,
            'farms' => $farms,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PayrollRun $payrollRun)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:2100',
            'status' => 'required|in:draft,finalized,paid',
            'generated_at' => 'nullable|date',
        ]);


        $validated['farm_id'] = $user->farm_id;
        $validated['user_id'] = $user->id;

        $data = PayrollRunData::from($validated);
        $payrollRun->update($data->toArray());

        return redirect()->route('payroll-runs.show', $payrollRun)->with('success', 'Payroll Run updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PayrollRun $payrollRun)
    {
        $payrollRun->delete();
        return redirect()->route('payroll-runs.index')->with('success', 'Payroll Run deleted successfully!');
    }
}
