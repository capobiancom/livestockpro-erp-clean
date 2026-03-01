<?php

namespace App\Policies;

use App\Models\MedicineGroup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicineGroupPolicy
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
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('medicine-groups.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicineGroup $medicineGroup): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicineGroup->farm_id) || $user->hasPermissionTo('medicine-groups.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('medicine-groups.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicineGroup $medicineGroup): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicineGroup->farm_id) || $user->hasPermissionTo('medicine-groups.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicineGroup $medicineGroup): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicineGroup->farm_id) || $user->hasPermissionTo('medicine-groups.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicineGroup $medicineGroup): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicineGroup->farm_id) || $user->hasPermissionTo('medicine-groups.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicineGroup $medicineGroup): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicineGroup->farm_id) || $user->hasPermissionTo('medicine-groups.forceDelete');
    }
}
