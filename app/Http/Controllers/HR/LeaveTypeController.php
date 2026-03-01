<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use App\Models\Farm;
use App\Models\User;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LeaveType::class, 'leave_type');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        $leaveTypes = LeaveType::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('HR/LeaveTypes/Index', [
            'leaveTypes' => $leaveTypes,
            'filters' => $filters,
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

        return Inertia::render('HR/LeaveTypes/Create', [
            'farms' => $farms,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveTypeRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validated()['user_id'] = $user->id;
        $request->validated()['farm_id'] = $user->farm_id;

        LeaveType::create($request->validated());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return Inertia::render('HR/LeaveTypes/Show', [
            'leaveType' => $leaveType->load(['farm', 'user']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType)
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

        return Inertia::render('HR/LeaveTypes/Edit', [
            'leaveType' => $leaveType,
            'farms' => $farms,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validated()['user_id'] = $user->id;
        $request->validated()['farm_id'] = $user->farm_id;

        $leaveType->update($request->validated());

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }


        $leaveType->delete();

        return redirect()->route('leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
}
