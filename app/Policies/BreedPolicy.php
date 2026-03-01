<?php

namespace App\Policies;

use App\Models\Breed;
use App\Models\User;

class BreedPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('breeds.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Breed $breed): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $breed->farm_id) || $user->hasPermissionTo('breeds.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('breeds.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Breed $breed): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $breed->farm_id) || $user->hasPermissionTo('breeds.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Breed $breed): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $breed->farm_id) || $user->hasPermissionTo('breeds.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Breed $breed): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $breed->farm_id) || $user->hasPermissionTo('breeds.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Breed $breed): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $breed->farm_id) || $user->hasPermissionTo('breeds.forceDelete');
    }
}
