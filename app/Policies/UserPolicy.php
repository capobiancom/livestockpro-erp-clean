<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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
     * Determine whether the user can manage any users.
     */
    public function manage(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('users.manage');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('farm owner') || $this->manage($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // A farm owner can view users belonging to their farm
        return ($user->hasRole('farm owner') && $user->farm_id === $model->farm_id) || $this->manage($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $this->manage($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // A farm owner can update users belonging to their farm, but not themselves if they are the farm owner
        return ($user->hasRole('farm owner') && $user->farm_id === $model->farm_id && $user->id !== $model->id) || $this->manage($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // A farm owner can delete users belonging to their farm, but not themselves if they are the farm owner
        return ($user->hasRole('farm owner') && $user->farm_id === $model->farm_id && $user->id !== $model->id) || $this->manage($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $model->farm_id) || $this->manage($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $model->farm_id) || $this->manage($user);
    }
}
