<?php

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChartOfAccountPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('chart-of-accounts.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $chartOfAccount->farm_id) || $user->hasPermissionTo('chart-of-accounts.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('chart-of-accounts.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $chartOfAccount->farm_id) || $user->hasPermissionTo('chart-of-accounts.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $chartOfAccount->farm_id) || $user->hasPermissionTo('chart-of-accounts.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $chartOfAccount->farm_id) || $user->hasPermissionTo('chart-of-accounts.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $chartOfAccount->farm_id) || $user->hasPermissionTo('chart-of-accounts.forceDelete');
    }
}
