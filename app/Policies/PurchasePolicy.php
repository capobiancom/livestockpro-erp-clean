<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('purchases.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Purchase $purchase): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $purchase->farm_id) || $user->hasPermissionTo('purchases.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('purchases.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Purchase $purchase): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $purchase->farm_id) || $user->hasPermissionTo('purchases.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Purchase $purchase): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $purchase->farm_id) || $user->hasPermissionTo('purchases.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Purchase $purchase): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $purchase->farm_id) || $user->hasPermissionTo('purchases.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Purchase $purchase): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $purchase->farm_id) || $user->hasPermissionTo('purchases.forceDelete');
    }
}
