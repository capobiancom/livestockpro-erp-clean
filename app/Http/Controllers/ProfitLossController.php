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

class ProfitLossController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('profitLoss', JournalEntry::class);

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->toDateString()
            : now()->startOfMonth()->toDateString();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->toDateString()
            : now()->toDateString();

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        // Profit & Loss should be for a period and should only include posted entries.
        // Sign convention:
        // - Income: credit increases => amount = credit - debit
        // - Expense: debit increases => amount = debit - credit
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
            ->whereDate('journal_entries.entry_date', '>=', $from)
            ->whereDate('journal_entries.entry_date', '<=', $to)
            ->groupBy('journal_entry_lines.account_id')
            ->get()
            ->keyBy('account_id');

        $rows = $accounts->map(function ($acc) use ($postedLines) {
            $line = $postedLines->get($acc->id);

            $debit = (float) ($line?->total_debit ?? 0);
            $credit = (float) ($line?->total_credit ?? 0);

            $type = $acc->type instanceof ChartOfAccountType ? $acc->type->value : (string) $acc->type;

            $amount = 0.0;
            if ($type === 'income') {
                $amount = $credit - $debit;
            } elseif ($type === 'expense') {
                $amount = $debit - $credit;
            }

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $type,
                'parent_id' => $acc->parent_id,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'amount' => round($amount, 2),
            ];
        });

        $plRows = $rows->whereIn('type', ['income', 'expense'])->values();

        $incomeRows = $plRows->where('type', 'income')->values();
        $expenseRows = $plRows->where('type', 'expense')->values();

        $totals = [
            'total_income' => round($incomeRows->sum('amount'), 2),
            'total_expenses' => round($expenseRows->sum('amount'), 2),
        ];
        $totals['net_profit'] = round($totals['total_income'] - $totals['total_expenses'], 2);

        return Inertia::render('Accounts/ProfitLoss', [
            'from' => $from,
            'to' => $to,
            'rows' => $plRows,
            'totals' => $totals,
        ]);
    }
}
