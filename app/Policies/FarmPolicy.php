<?php

namespace App\Policies;

use App\Models\Farm;
use App\Models\User;

class FarmPolicy
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
        // Farm owners can view farms they own. Super Admin can view all (via before()).
        return $user->hasRole('farm owner') || $user->hasPermissionTo('farms.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $farm->user_id === $user->id) || $user->hasPermissionTo('farms.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Farm owners should not be able to create farms
        return !$user->hasRole('farm owner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $farm->user_id === $user->id) || $user->hasPermissionTo('farms.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farm $farm): bool
    {
        // Super Admin can delete farms they own
        return $user->hasRole('Super Admin') || $user->hasPermissionTo('farms.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Farm $farm): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermissionTo('farms.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Farm $farm): bool
    {
        return $user->hasRole('Super Admin') || $user->hasPermissionTo('farms.forceDelete');
    }
}
