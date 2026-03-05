<?php

namespace App\Policies;

use App\Models\ReproductionRecord;
use App\Models\User;

class ReproductionRecordPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('reproduction-records.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReproductionRecord $reproductionRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $reproductionRecord->farm_id) || $user->hasPermissionTo('reproduction-records.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('reproduction-records.create');
    }

    /**
     * Determine whether the user can conseption success rate report.
     */
    public function conseptionSuccessRateReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('reproduction-records.conseption_success_rate_reports');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReproductionRecord $reproductionRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $reproductionRecord->farm_id) || $user->hasPermissionTo('reproduction-records.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReproductionRecord $reproductionRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $reproductionRecord->farm_id) || $user->hasPermissionTo('reproduction-records.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReproductionRecord $reproductionRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $reproductionRecord->farm_id) || $user->hasPermissionTo('reproduction-records.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReproductionRecord $reproductionRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $reproductionRecord->farm_id) || $user->hasPermissionTo('reproduction-records.forceDelete');
    }
}
