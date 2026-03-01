<?php

namespace App\Policies;

use App\Models\StaffProfile;
use App\Models\User;

class StaffProfilePolicy
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
        return $user->hasRole('farm owner') || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StaffProfile $s): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $s->farm_id) || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaffProfile $s): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $s->farm_id) || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaffProfile $s): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $s->farm_id) || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StaffProfile $s): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $s->farm_id) || $user->hasPermissionTo('staff.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StaffProfile $s): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $s->farm_id) || $user->hasPermissionTo('staff.manage');
    }
}
