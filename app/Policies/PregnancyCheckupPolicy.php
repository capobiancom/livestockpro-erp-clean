<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PregnancyCheckup;
use Illuminate\Auth\Access\Response;

class PregnancyCheckupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['farm owner', 'vet', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PregnancyCheckup $pregnancyCheckup): bool
    {
        return $user->hasRole(['farm owner', 'vet', 'staff']) && $user->farm_id === $pregnancyCheckup->pregnancy->farm_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['farm owner', 'vet']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PregnancyCheckup $pregnancyCheckup): bool
    {
        return $user->hasRole(['farm owner', 'vet']) && $user->farm_id === $pregnancyCheckup->pregnancy->farm_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PregnancyCheckup $pregnancyCheckup): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id === $pregnancyCheckup->pregnancy->farm_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PregnancyCheckup $pregnancyCheckup): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id === $pregnancyCheckup->pregnancy->farm_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PregnancyCheckup $pregnancyCheckup): bool
    {
        return false; // Typically, force delete is not allowed or handled by system admins
    }
}
