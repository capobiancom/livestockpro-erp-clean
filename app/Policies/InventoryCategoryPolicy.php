<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryCategoryPolicy
{
    use HandlesAuthorization;

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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('categories.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $category->farm_id) || $user->hasPermissionTo('categories.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('categories.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $category->farm_id) || $user->hasPermissionTo('categories.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $category->farm_id) || $user->hasPermissionTo('categories.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $category->farm_id) || $user->hasPermissionTo('categories.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $category->farm_id) || $user->hasPermissionTo('categories.forceDelete');
    }
}
