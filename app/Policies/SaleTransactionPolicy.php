<?php

namespace App\Policies;

use App\Models\SaleTransaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SaleTransactionPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('customer-payments.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SaleTransaction $saleTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $saleTransaction->farm_id) || $user->hasPermissionTo('customer-payments.view');
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('customer-payments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SaleTransaction $saleTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $saleTransaction->farm_id) || $user->hasPermissionTo('customer-payments.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SaleTransaction $saleTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $saleTransaction->farm_id) || $user->hasPermissionTo('customer-payments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SaleTransaction $saleTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $saleTransaction->farm_id) || $user->hasPermissionTo('customer-payments.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SaleTransaction $saleTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $saleTransaction->farm_id) || $user->hasPermissionTo('customer-payments.forceDelete');
    }
}
