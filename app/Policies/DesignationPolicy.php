<?php

namespace App\Policies;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DesignationPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('designations.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Designation $designation): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $designation->farm_id) || $user->hasPermissionTo('designations.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('designations.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Designation $designation): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $designation->farm_id) || $user->hasPermissionTo('designations.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Designation $designation): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $designation->farm_id) || $user->hasPermissionTo('designations.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Designation $designation): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $designation->farm_id) || $user->hasPermissionTo('designations.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Designation $designation): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $designation->farm_id) || $user->hasPermissionTo('designations.forceDelete');
    }
}
