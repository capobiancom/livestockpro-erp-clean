<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('sales.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sale $sale): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $sale->farm_id) || $user->hasPermissionTo('sales.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('sales.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sale $sale): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $sale->farm_id) || $user->hasPermissionTo('sales.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $sale): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $sale->farm_id) || $user->hasPermissionTo('sales.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sale $sale): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $sale->farm_id) || $user->hasPermissionTo('sales.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sale $sale): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $sale->farm_id) || $user->hasPermissionTo('sales.forceDelete');
    }
}
