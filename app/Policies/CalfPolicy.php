<?php

namespace App\Policies;

use App\Models\Calf;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CalfPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('calves.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Calf $calf): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $calf->farm_id) || $user->hasPermissionTo('calves.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('calves.create');
    }

    /**
     * Determine whether the user can create models.
     */
    public function calvingIntervalReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('calves.calvingIntervalReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Calf $calf): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $calf->farm_id) || $user->hasPermissionTo('calves.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Calf $calf): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $calf->farm_id) || $user->hasPermissionTo('calves.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Calf $calf): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $calf->farm_id) || $user->hasPermissionTo('calves.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Calf $calf): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $calf->farm_id) || $user->hasPermissionTo('calves.forceDelete');
    }
}
