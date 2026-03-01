<?php

namespace App\Policies;

use App\Models\Logistic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LogisticPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('logistics.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Logistic $logistic): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $logistic->farm_id) || $user->hasPermissionTo('logistics.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('logistics.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Logistic $logistic): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $logistic->farm_id) || $user->hasPermissionTo('logistics.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Logistic $logistic): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $logistic->farm_id) || $user->hasPermissionTo('logistics.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Logistic $logistic): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $logistic->farm_id) || $user->hasPermissionTo('logistics.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Logistic $logistic): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $logistic->farm_id) || $user->hasPermissionTo('logistics.forceDelete');
    }
}
