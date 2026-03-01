<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockMovement;

class StockMovementPolicy
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
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StockMovement $stockMovement): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $stockMovement->farm_id) || $user->hasPermissionTo('stock-movements.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.create');
    }

    /**
     * Determine whether the user can view current stock by item report.
     */
    public function currentStockByItemReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.currentStockByItemReport');
    }

    /**
     * Determine whether the user can view current stock by item report.
     */
    public function lowStockAlertReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.lowStockAlertReport');
    }

    /**
     * Determine whether the user can view current stock by item report.
     */
    public function expiredMedicineAlertReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.expiredMedicineAlertReport');
    }

    /**
     * Determine whether the user can view current stock by item report.
     */
    public function feedConsumedPerAnimalReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.feedConsumedPerAnimalReport');
    }

    /**
     * Determine whether the user can view cost of feed per cow report.
     */
    public function costOfFeedPerCowReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.costOfFeedPerCowReport');
    }

    /**
     * Determine whether the user can view monthly consumption summery report.
     */
    public function monthlyConsumptionSummeryReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.monthlyConsumptionSummeryReport');
    }

    /**
     * Determine whether the user can view wastage and loss report.
     */
    public function wastageAndLossReport(User $user): bool
    {
        return $user->hasRole('farm owner') && $user->farm_id !== null || $user->hasPermissionTo('stock-movements.wastageAndLossReport');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StockMovement $stockMovement): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $stockMovement->farm_id) || $user->hasPermissionTo('stock-movements.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $stockMovement->farm_id) || $user->hasPermissionTo('stock-movements.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StockMovement $stockMovement): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $stockMovement->farm_id) || $user->hasPermissionTo('stock-movements.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StockMovement $stockMovement): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $stockMovement->farm_id) || $user->hasPermissionTo('stock-movements.forceDelete');
    }
}
