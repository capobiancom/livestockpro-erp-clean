<?php

namespace App\Policies;

use App\Models\VaccinationRecord;
use App\Models\User;

class VaccinationRecordPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('vaccinations.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VaccinationRecord $v): bool
    {
        // Farm owner can view any record, or user has specific permission
        return ($user->hasRole('farm owner') && $user->farm_id === $v->farm_id) || $user->hasPermissionTo('vaccinations.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('vaccinations.create');
    }

    /**
     * Determine whether the user can view vaccination due report.
     */
    public function vaccinationDueReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('vaccinations.vaccinationDueReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VaccinationRecord $v): bool
    {
        // Farm owner can update any record, or user has specific permission
        return ($user->hasRole('farm owner') && $user->farm_id === $v->farm_id) || $user->hasPermissionTo('vaccinations.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VaccinationRecord $v): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $v->farm_id) || $user->hasPermissionTo('vaccinations.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VaccinationRecord $v): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $v->farm_id) || $user->hasPermissionTo('vaccinations.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VaccinationRecord $v): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $v->farm_id) || $user->hasPermissionTo('vaccinations.forceDelete');
    }
}
