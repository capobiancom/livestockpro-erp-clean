<?php

namespace App\Policies;

use App\Models\HealthIssue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class HealthIssuePolicy
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
        return $user->hasRole('farm owner') || $user->hasPermissionTo('health-issues.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, HealthIssue $healthIssue): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthIssue->farm_id) || $user->hasPermissionTo('health-issues.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('health-issues.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HealthIssue $healthIssue): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthIssue->farm_id) || $user->hasPermissionTo('health-issues.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, HealthIssue $healthIssue): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthIssue->farm_id) || $user->hasPermissionTo('health-issues.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, HealthIssue $healthIssue): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthIssue->farm_id) || $user->hasPermissionTo('health-issues.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, HealthIssue $healthIssue): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $healthIssue->farm_id) || $user->hasPermissionTo('health-issues.forceDelete');
    }
}
