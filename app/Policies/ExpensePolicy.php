<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('expenses.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $expense->farm_id) || $user->hasPermissionTo('expenses.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('expenses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $expense->farm_id) || $user->hasPermissionTo('expenses.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $expense->farm_id) || $user->hasPermissionTo('expenses.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $expense->farm_id) || $user->hasPermissionTo('expenses.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $expense->farm_id) || $user->hasPermissionTo('expenses.forceDelete');
    }
}
