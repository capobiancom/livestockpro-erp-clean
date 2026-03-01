<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShiftPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('shifts.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shift $shift): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $shift->farm_id) || $user->hasPermissionTo('shifts.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('shifts.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Shift $shift): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $shift->farm_id) || $user->hasPermissionTo('shifts.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Shift $shift): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $shift->farm_id) || $user->hasPermissionTo('shifts.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Shift $shift): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $shift->farm_id) || $user->hasPermissionTo('shifts.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Shift $shift): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $shift->farm_id) || $user->hasPermissionTo('shifts.forceDelete');
    }
}
