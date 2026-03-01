<?php

namespace App\Policies;

use App\Models\FeedingRecord;
use App\Models\User;

class FeedingRecordPolicy
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
        return $user->hasRole('farm owner') || $user->hasPermissionTo('feedings.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FeedingRecord $f): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $f->farm_id) || $user->hasPermissionTo('feedings.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null)  || $user->hasPermissionTo('feedings.create');
    }

    /**
     * Determine whether the user view feeding cost analysis.
     */
    public function feedingCostAnalysis(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null)  || $user->hasPermissionTo('feedings.feedingCostAnalysis');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FeedingRecord $f): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $f->farm_id) || $user->hasPermissionTo('feedings.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FeedingRecord $f): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $f->farm_id) || $user->hasPermissionTo('feedings.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FeedingRecord $f): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $f->farm_id) || $user->hasPermissionTo('feedings.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FeedingRecord $f): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $f->farm_id) || $user->hasPermissionTo('feedings.forceDelete');
    }
}
