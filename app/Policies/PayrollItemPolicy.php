<?php

namespace App\Policies;

use App\Models\PayrollItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PayrollItemPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('payroll-items.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PayrollItem $payrollItem): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $payrollItem->payrollRun->farm_id) || $user->hasPermissionTo('payroll-items.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('payroll-items.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PayrollItem $payrollItem): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $payrollItem->payrollRun->farm_id) || $user->hasPermissionTo('payroll-items.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PayrollItem $payrollItem): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $payrollItem->payrollRun->farm_id) || $user->hasPermissionTo('payroll-items.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PayrollItem $payrollItem): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $payrollItem->payrollRun->farm_id) || $user->hasPermissionTo('payroll-items.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PayrollItem $payrollItem): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $payrollItem->payrollRun->farm_id) || $user->hasPermissionTo('payroll-items.forceDelete');
    }
}
