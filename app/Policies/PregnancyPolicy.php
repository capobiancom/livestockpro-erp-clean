<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pregnancy;
use Illuminate\Auth\Access\Response;

class PregnancyPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('pregnancies.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pregnancy $pregnancy): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $pregnancy->farm_id) || $user->hasPermissionTo('pregnancies.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('pregnancies.create');
    }

    /**
     * Determine whether the user can create models.
     */
    public function pregnancyLossAnalysis(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('pregnancies.pregnancyLossAnalysis');
    }

    /**
     * Determine whether the user can create models.
     */
    public function fertilityPerformancePerCowReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('pregnancies.fertilityPerformancePerCowReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pregnancy $pregnancy): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $pregnancy->farm_id) || $user->hasPermissionTo('pregnancies.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pregnancy $pregnancy): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $pregnancy->farm_id) || $user->hasPermissionTo('pregnancies.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pregnancy $pregnancy): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $pregnancy->farm_id) || $user->hasPermissionTo('pregnancies.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pregnancy $pregnancy): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $pregnancy->farm_id) || $user->hasPermissionTo('pregnancies.forceDelete');
    }
}
