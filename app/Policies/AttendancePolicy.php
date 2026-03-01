<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('attendances.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $attendance->farm_id) || $user->hasPermissionTo('attendances.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('attendances.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $attendance->farm_id) || $user->hasPermissionTo('attendances.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $attendance->farm_id) || $user->hasPermissionTo('attendances.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $attendance->farm_id) || $user->hasPermissionTo('attendances.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $attendance->farm_id) || $user->hasPermissionTo('attendances.forceDelete');
    }
}
