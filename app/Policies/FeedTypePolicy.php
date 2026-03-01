<?php

namespace App\Policies;

use App\Models\FeedType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeedTypePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('feed_types.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeedType $feedType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $feedType->farm_id) || $user->hasPermissionTo('feed_types.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('feed_types.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeedType $feedType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $feedType->farm_id) || $user->hasPermissionTo('feed_types.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeedType $feedType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $feedType->farm_id) || $user->hasPermissionTo('feed_types.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeedType $feedType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $feedType->farm_id) || $user->hasPermissionTo('feed_types.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeedType $feedType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $feedType->farm_id) || $user->hasPermissionTo('feed_types.forceDelete');
    }
}
