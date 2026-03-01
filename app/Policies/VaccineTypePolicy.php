<?php

namespace App\Policies;

use App\Models\VaccineType;
use App\Models\User;

class VaccineTypePolicy
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
        return $user->hasRole('farm owner') || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VaccineType $vt): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $vt->farm_id) || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VaccineType $vt): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $vt->farm_id) || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VaccineType $vt): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $vt->farm_id) || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VaccineType $vt): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $vt->farm_id) || $user->hasPermissionTo('vaccine_types.manage');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VaccineType $vt): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $vt->farm_id) || $user->hasPermissionTo('vaccine_types.manage');
    }
}
