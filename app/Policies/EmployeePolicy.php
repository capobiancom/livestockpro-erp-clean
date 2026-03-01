<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('employees.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $employee->farm_id) || $user->hasPermissionTo('employees.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('employees.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $employee->farm_id) || $user->hasPermissionTo('employees.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $employee->farm_id) || $user->hasPermissionTo('employees.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $employee->farm_id) || $user->hasPermissionTo('employees.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $employee->farm_id) || $user->hasPermissionTo('employees.forceDelete');
    }
}
