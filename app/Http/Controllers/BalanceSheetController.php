<?php

namespace App\Http\Controllers;

use App\Enums\ChartOfAccountType;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\View\View;

class BalanceSheetController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('balanceSheet', JournalEntry::class);

        $asOf = $request->input('as_of')
            ? Carbon::parse($request->input('as_of'))->toDateString()
            : now()->toDateString();

        // Balance Sheet should be "as of" a date, and should only include posted entries.
        // Sign convention:
        // - Assets, Expenses: debit increases => balance = debit - credit
        // - Liabilities, Equity, Income: credit increases => balance = credit - debit
        $accounts = ChartOfAccount::query()
            ->select(['id', 'code', 'name', 'type', 'parent_id'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $postedLines = JournalEntryLine::query()
            ->selectRaw('journal_entry_lines.account_id as account_id')
            ->selectRaw('SUM(journal_entry_lines.debit_amount) as total_debit')
            ->selectRaw('SUM(journal_entry_lines.credit_amount) as total_credit')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->groupBy('journal_entry_lines.account_id')
            ->get()
            ->keyBy('account_id');

        $rows = $accounts->map(function ($acc) use ($postedLines) {
            $line = $postedLines->get($acc->id);

            $debit = (float) ($line?->total_debit ?? 0);
            $credit = (float) ($line?->total_credit ?? 0);

            $type = $acc->type instanceof ChartOfAccountType ? $acc->type->value : (string) $acc->type;

            $balance = 0.0;
            if (in_array($type, ['asset', 'expense'], true)) {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $type,
                'parent_id' => $acc->parent_id,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'balance' => round($balance, 2),
            ];
        });

        // Only Balance Sheet sections
        $bsRows = $rows->whereIn('type', ['asset', 'liability', 'equity'])->values();

        $totals = [
            'assets' => round($bsRows->where('type', 'asset')->sum('balance'), 2),
            'liabilities' => round($bsRows->where('type', 'liability')->sum('balance'), 2),
            'equity' => round($bsRows->where('type', 'equity')->sum('balance'), 2),
        ];
        $totals['liabilities_and_equity'] = round($totals['liabilities'] + $totals['equity'], 2);
        $totals['difference'] = round($totals['assets'] - $totals['liabilities_and_equity'], 2);

        return Inertia::render('Accounts/BalanceSheet', [
            'asOf' => $asOf,
            'rows' => $bsRows,
            'totals' => $totals,
        ]);
    }

    public function print(Request $request): View
    {
        $this->authorize('balanceSheet', JournalEntry::class);

        $asOf = $request->input('as_of')
            ? Carbon::parse($request->input('as_of'))->toDateString()
            : now()->toDateString();

        $accounts = ChartOfAccount::query()
            ->select(['id', 'code', 'name', 'type', 'parent_id'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $postedLines = JournalEntryLine::query()
            ->selectRaw('journal_entry_lines.account_id as account_id')
            ->selectRaw('SUM(journal_entry_lines.debit_amount) as total_debit')
            ->selectRaw('SUM(journal_entry_lines.credit_amount) as total_credit')
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.status', 'posted')
            ->whereDate('journal_entries.entry_date', '<=', $asOf)
            ->groupBy('journal_entry_lines.account_id')
            ->get()
            ->keyBy('account_id');

        $rows = $accounts->map(function ($acc) use ($postedLines) {
            $line = $postedLines->get($acc->id);

            $debit = (float) ($line?->total_debit ?? 0);
            $credit = (float) ($line?->total_credit ?? 0);

            $type = $acc->type instanceof ChartOfAccountType ? $acc->type->value : (string) $acc->type;

            $balance = 0.0;
            if (in_array($type, ['asset', 'expense'], true)) {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $type,
                'parent_id' => $acc->parent_id,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'balance' => round($balance, 2),
            ];
        });

        $bsRows = $rows->whereIn('type', ['asset', 'liability', 'equity'])->values();

        $totals = [
            'assets' => round($bsRows->where('type', 'asset')->sum('balance'), 2),
            'liabilities' => round($bsRows->where('type', 'liability')->sum('balance'), 2),
            'equity' => round($bsRows->where('type', 'equity')->sum('balance'), 2),
        ];
        $totals['liabilities_and_equity'] = round($totals['liabilities'] + $totals['equity'], 2);
        $totals['difference'] = round($totals['assets'] - $totals['liabilities_and_equity'], 2);

        return view('reports.balance-sheet.print', [
            'asOf' => $asOf,
            'rows' => $bsRows,
            'totals' => $totals,
        ]);
    }
}
