<?php

namespace App\Policies;

use App\Models\CashTransaction;
use App\Models\User;

class CashTransactionPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('cash-transactions.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CashTransaction $cashTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $cashTransaction->farm_id) || $user->hasPermissionTo('cash-transactions.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('cash-transactions.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CashTransaction $cashTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $cashTransaction->farm_id) || $user->hasPermissionTo('cash-transactions.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CashTransaction $cashTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $cashTransaction->farm_id) || $user->hasPermissionTo('cash-transactions.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CashTransaction $cashTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $cashTransaction->farm_id) || $user->hasPermissionTo('cash-transactions.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CashTransaction $cashTransaction): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $cashTransaction->farm_id) || $user->hasPermissionTo('cash-transactions.forceDelete');
    }
}
