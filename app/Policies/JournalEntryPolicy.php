<?php

namespace App\Policies;

use App\Models\JournalEntry;
use App\Models\User;

class JournalEntryPolicy
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
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.manage');
    }

    /**
     * Determine whether the user can view voucher report.
     */
    public function voucherReport(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.voucher_report');
    }

    /**
     * Determine whether the user can view balance sheet report.
     */
    public function balanceSheet(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.balance_sheet');
    }

    /**
     * Determine whether the user can view profit loss report.
     */
    public function profitLoss(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.profit_loss');
    }

    /**
     * Determine whether the user can view cash flow report.
     */
    public function cashFlow(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.cash_flow');
    }

    /**
     * Determine whether the user can view Trial balance report.
     */
    public function TrialBalance(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.trial_balance');
    }

    /**
     * Determine whether the user can view fixed asset report.
     */
    public function fixedAsset(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.fixed_asset');
    }

    /**
     * Determine whether the user can view financial report.
     */
    public function financialReport(User $user): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id !== null) || $user->hasPermissionTo('journal-entries.financial_reports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JournalEntry $journalEntry): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $journalEntry->farm_id) || $user->hasPermissionTo('journal-entries.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('farm owner') || $user->hasPermissionTo('journal-entries.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JournalEntry $journalEntry): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $journalEntry->farm_id) || $user->hasPermissionTo('journal-entries.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JournalEntry $journalEntry): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $journalEntry->farm_id) || $user->hasPermissionTo('journal-entries.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JournalEntry $journalEntry): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $journalEntry->farm_id) || $user->hasPermissionTo('journal-entries.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JournalEntry $journalEntry): bool
    {
        return ($user->hasRole('farm owner') && $user->farm_id === $journalEntry->farm_id) || $user->hasPermissionTo('journal-entries.forceDelete');
    }
}
