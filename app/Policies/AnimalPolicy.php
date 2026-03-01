<?php

namespace App\Policies;

use App\Models\Animal;
use App\Models\User;

class AnimalPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('animals.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Animal $animal): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $animal->farm_id) || $user->hasPermissionTo('animals.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('animals.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Animal $animal): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $animal->farm_id) || $user->hasPermissionTo('animals.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Animal $animal): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $animal->farm_id) || $user->hasPermissionTo('animals.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Animal $animal): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $animal->farm_id) || $user->hasPermissionTo('animals.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Animal $animal): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $animal->farm_id) || $user->hasPermissionTo('animals.forceDelete');
    }
}
