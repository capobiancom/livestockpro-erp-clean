<?php

namespace App\Policies;

use App\Models\HealthEvent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HealthEventPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('health-events.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HealthEvent $healthEvent): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthEvent->farm_id) || $user->hasPermissionTo('health-events.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null)  || $user->hasPermissionTo('health-events.create');
    }

    /**
     * Determine whether the user can view health report.
     */
    public function animalHealthReport(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null)  || $user->hasPermissionTo('health-events.animalHealthReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HealthEvent $healthEvent): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthEvent->farm_id) || $user->hasPermissionTo('health-events.update');
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HealthEvent $healthEvent): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthEvent->farm_id) || $user->hasPermissionTo('health-events.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HealthEvent $healthEvent): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthEvent->farm_id) || $user->hasPermissionTo('health-events.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HealthEvent $healthEvent): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthEvent->farm_id) || $user->hasPermissionTo('health-events.forceDelete');
    }
}
