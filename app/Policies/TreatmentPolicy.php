<?php

namespace App\Policies;

use App\Models\Treatment;
use App\Models\User;

class TreatmentPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('treatments.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Treatment $treatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $treatment->farm_id) || $user->hasPermissionTo('treatments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('treatments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Treatment $treatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $treatment->farm_id) || $user->hasPermissionTo('treatments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Treatment $treatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $treatment->farm_id) || $user->hasPermissionTo('treatments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Treatment $treatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $treatment->farm_id) || $user->hasPermissionTo('treatments.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Treatment $treatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $treatment->farm_id) || $user->hasPermissionTo('treatments.forceDelete');
    }
}
