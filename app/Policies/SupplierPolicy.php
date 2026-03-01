<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('suppliers.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Supplier $supplier): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplier->farm_id) || $user->hasPermissionTo('suppliers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id || $user->hasPermissionTo('suppliers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplier->farm_id) || $user->hasPermissionTo('suppliers.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplier->farm_id) || $user->hasPermissionTo('suppliers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Supplier $supplier): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplier->farm_id) || $user->hasPermissionTo('suppliers.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Supplier $supplier): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplier->farm_id) || $user->hasPermissionTo('suppliers.forceDelete');
    }
}
