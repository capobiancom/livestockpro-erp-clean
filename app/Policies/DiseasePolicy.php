<?php

namespace App\Policies;

use App\Models\Disease;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiseasePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('diseases.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Disease $disease): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $disease->farm_id) || $user->hasPermissionTo('diseases.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('diseases.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Disease $disease): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $disease->farm_id) || $user->hasPermissionTo('diseases.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Disease $disease): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $disease->farm_id) || $user->hasPermissionTo('diseases.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Disease $disease): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $disease->farm_id) || $user->hasPermissionTo('diseases.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Disease $disease): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $disease->farm_id) || $user->hasPermissionTo('diseases.forceDelete');
    }
}
