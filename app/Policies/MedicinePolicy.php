<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicinePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('medicines.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medicine $medicine): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicine->farm_id) || $user->hasPermissionTo('medicines.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('medicines.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Medicine $medicine): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicine->farm_id) || $user->hasPermissionTo('medicines.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Medicine $medicine): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicine->farm_id) || $user->hasPermissionTo('medicines.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Medicine $medicine): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicine->farm_id) || $user->hasPermissionTo('medicines.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Medicine $medicine): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $medicine->farm_id) || $user->hasPermissionTo('medicines.forceDelete');
    }
}
