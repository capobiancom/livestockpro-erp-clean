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
        // A farm owner can view their own farm. Super Admin can view all.
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('farms.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $farm->id) || $user->hasPermissionTo('farms.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Super Admin or a user with specific permission can create new farms.
        // A farm owner typically manages their existing farm, not creates new ones.
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('farms.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $farm->id) || $user->hasPermissionTo('farms.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $farm->id) || $user->hasPermissionTo('farms.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $farm->id) || $user->hasPermissionTo('farms.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Farm $farm): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $farm->id) || $user->hasPermissionTo('farms.forceDelete');
    }
}
