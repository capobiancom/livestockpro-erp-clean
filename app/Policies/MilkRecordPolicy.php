<?php

namespace App\Policies;

use App\Models\MilkRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MilkRecordPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('milk-records.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MilkRecord $milkRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $milkRecord->farm_id) || $user->hasPermissionTo('milk-records.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('milk-records.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MilkRecord $milkRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $milkRecord->farm_id) || $user->hasPermissionTo('milk-records.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MilkRecord $milkRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $milkRecord->farm_id) || $user->hasPermissionTo('milk-records.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MilkRecord $milkRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $milkRecord->farm_id) || $user->hasPermissionTo('milk-records.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MilkRecord $milkRecord): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $milkRecord->farm_id) || $user->hasPermissionTo('milk-records.forceDelete');
    }
}
