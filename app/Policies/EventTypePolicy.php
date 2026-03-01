<?php

namespace App\Policies;

use App\Models\EventType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventTypePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('event-types.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EventType $eventType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $eventType->farm_id) || $user->hasPermissionTo('event-types.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('event-types.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EventType $eventType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $eventType->farm_id) || $user->hasPermissionTo('event-types.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventType $eventType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $eventType->farm_id) || $user->hasPermissionTo('event-types.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventType $eventType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $eventType->farm_id) || $user->hasPermissionTo('event-types.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventType $eventType): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $eventType->farm_id) || $user->hasPermissionTo('event-types.forceDelete');
    }
}
