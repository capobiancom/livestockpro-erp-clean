<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('customers.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Customer $customer): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $customer->farm_id) || $user->hasPermissionTo('customers.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('customers.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Customer $customer): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $customer->farm_id) || $user->hasPermissionTo('customers.update');
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $customer->farm_id) || $user->hasPermissionTo('customers.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Customer $customer): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $customer->farm_id) || $user->hasPermissionTo('customers.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Customer $customer): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $customer->farm_id) || $user->hasPermissionTo('customers.forceDelete');
    }
}
