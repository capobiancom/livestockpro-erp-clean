<?php

namespace App\Policies;

use App\Models\DiseaseTreatment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiseaseTreatmentPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('disease-treatments.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DiseaseTreatment $diseaseTreatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $diseaseTreatment->farm_id) || $user->hasPermissionTo('disease-treatments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('disease-treatments.create');
    }

    /**
     * Determine whether the user can treatment cost per cow report.
     */
    public function treatmentCostPerCowReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('disease-treatments.treatment_cost_per_cow_reports');
    }

    /**
     * Determine whether the user can treatment cost per cow report.
     */
    public function medicineUseedPerDiseaseReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('disease-treatments.medicineUseedPerDiseaseReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DiseaseTreatment $diseaseTreatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $diseaseTreatment->farm_id) || $user->hasPermissionTo('disease-treatments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DiseaseTreatment $diseaseTreatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $diseaseTreatment->farm_id) || $user->hasPermissionTo('disease-treatments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DiseaseTreatment $diseaseTreatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $diseaseTreatment->farm_id) || $user->hasPermissionTo('disease-treatments.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DiseaseTreatment $diseaseTreatment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $diseaseTreatment->farm_id) || $user->hasPermissionTo('disease-treatments.forceDelete');
    }
}
