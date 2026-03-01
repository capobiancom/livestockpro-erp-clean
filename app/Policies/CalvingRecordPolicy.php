<?php

namespace App\Policies;

use App\Models\CalvingRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CalvingRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasRole('employee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CalvingRecord $calvingRecord): bool
    {
        return $user->farm_id === $calvingRecord->farm_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasRole('employee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalvingRecord $calvingRecord): bool
    {
        return $user->farm_id === $calvingRecord->farm_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalvingRecord $calvingRecord): bool
    {
        return $user->farm_id === $calvingRecord->farm_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CalvingRecord $calvingRecord): bool
    {
        return $user->farm_id === $calvingRecord->farm_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CalvingRecord $calvingRecord): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id === $calvingRecord->farm_id;
    }
}
