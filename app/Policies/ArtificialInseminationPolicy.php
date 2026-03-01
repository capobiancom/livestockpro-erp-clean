<?php

namespace App\Policies;

use App\Models\ArtificialInsemination;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArtificialInseminationPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('artificial-inseminations.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ArtificialInsemination $artificialInsemination): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $artificialInsemination->farm_id) || $user->hasPermissionTo('artificial-inseminations.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('artificial-inseminations.create');
    }

    /**
     * Determine whether the user can AI vs arificial insemination report.
     */
    public function aiVsNaturalBreedingSuccessReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('artificial-inseminations.aiVsNaturalBreedingSuccessReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArtificialInsemination $artificialInsemination): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $artificialInsemination->farm_id) || $user->hasPermissionTo('artificial-inseminations.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArtificialInsemination $artificialInsemination): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $artificialInsemination->farm_id) || $user->hasPermissionTo('artificial-inseminations.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArtificialInsemination $artificialInsemination): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $artificialInsemination->farm_id) || $user->hasPermissionTo('artificial-inseminations.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArtificialInsemination $artificialInsemination): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $artificialInsemination->farm_id) || $user->hasPermissionTo('artificial-inseminations.forceDelete');
    }
}
