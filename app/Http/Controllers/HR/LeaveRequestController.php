<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Farm;
use App\Models\User;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Http\Requests\UpdateLeaveRequestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LeaveRequest::class, 'leave_request');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status']);
        $leaveRequests = LeaveRequest::query()
            ->with(['employee', 'leaveType', 'farm', 'user'])
            ->when($request->input('search'), function ($query, $search) {
                $query->whereHas('employee', function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('leaveType', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when((Auth::user() instanceof User) && Auth::user()->hasRole('farm owner'), function ($query) {
                $query->where('farm_id', Auth::user()->farm_id);
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('HR/LeaveRequests/Index', [
            'leaveRequests' => $leaveRequests,
            'filters' => $filters,
            'statuses' => ['pending', 'approved', 'rejected'],
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

        $employees = Employee::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $leaveTypes = LeaveType::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $farms = Farm::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        return Inertia::render('HR/LeaveRequests/Create', [
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
            'farms' => $farms,
            'users' => User::with('employee')->get(), // For approved_by dropdown, if needed
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequestRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['farm_id'] = $user->farm_id;

        LeaveRequest::create($validated);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        return Inertia::render('HR/LeaveRequests/Show', [
            'leaveRequest' => $leaveRequest->load(['employee', 'leaveType', 'farm', 'user', 'approvedBy']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $leaveTypes = LeaveType::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $farms = Farm::when(($user instanceof User) && $user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();

        return Inertia::render('HR/LeaveRequests/Edit', [
            'leaveRequest' => $leaveRequest->load(['employee', 'leaveType', 'farm', 'user', 'approvedBy']),
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
            'farms' => $farms,
            'users' => User::with('employee')->get(), // For approved_by dropdown, if needed
            'statuses' => ['pending', 'approved', 'rejected'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['farm_id'] = $user->farm_id;

        $leaveRequest->update($validated);

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }
}
