<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveRequestPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('leave-requests.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $leaveRequest->farm_id) || $user->hasPermissionTo('leave-requests.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('leave-requests.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $leaveRequest->farm_id) || $user->hasPermissionTo('leave-requests.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $leaveRequest->farm_id) || $user->hasPermissionTo('leave-requests.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $leaveRequest->farm_id) || $user->hasPermissionTo('leave-requests.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $leaveRequest->farm_id) || $user->hasPermissionTo('leave-requests.forceDelete');
    }
}
