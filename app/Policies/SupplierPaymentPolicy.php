<?php

namespace App\Policies;

use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierPaymentPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('supplier-payments.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupplierPayment $supplierPayment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplierPayment->farm_id) || $user->hasPermissionTo('supplier-payments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('supplier-payments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupplierPayment $supplierPayment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplierPayment->farm_id) || $user->hasPermissionTo('supplier-payments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupplierPayment $supplierPayment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplierPayment->farm_id) || $user->hasPermissionTo('supplier-payments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupplierPayment $supplierPayment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplierPayment->farm_id) || $user->hasPermissionTo('supplier-payments.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupplierPayment $supplierPayment): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $supplierPayment->farm_id) || $user->hasPermissionTo('supplier-payments.forceDelete');
    }
}
