<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('inventory.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoryItem $item): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $item->farm_id) || $user->hasPermissionTo('inventory.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('inventory.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoryItem $item): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $item->farm_id) || $user->hasPermissionTo('inventory.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoryItem $item): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $item->farm_id) || $user->hasPermissionTo('inventory.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventoryItem $item): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $item->farm_id) || $user->hasPermissionTo('inventory.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventoryItem $item): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $item->farm_id) || $user->hasPermissionTo('inventory.forceDelete');
    }
}
