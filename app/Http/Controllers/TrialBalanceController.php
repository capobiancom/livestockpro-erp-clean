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

class TrialBalanceController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('TrialBalance', JournalEntry::class);

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->toDateString()
            : now()->startOfMonth()->toDateString();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->toDateString()
            : now()->toDateString();

        // Trial Balance best practices:
        // - Use posted journal entries only
        // - Show totals for debit and credit and ensure they match
        // - Provide a clear date range and optional "include zero balances"
        $includeZero = filter_var($request->input('include_zero', false), FILTER_VALIDATE_BOOLEAN);

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

            // For Trial Balance, we present net balance split into Debit/Credit columns.
            // Net = debit - credit. If positive => debit balance, else credit balance.
            $net = $debit - $credit;

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $type,
                'parent_id' => $acc->parent_id,
                'debit_total' => round($debit, 2),
                'credit_total' => round($credit, 2),
                'net' => round($net, 2),
                'debit_balance' => round(max($net, 0), 2),
                'credit_balance' => round(max(-$net, 0), 2),
            ];
        });

        if (! $includeZero) {
            $rows = $rows->filter(fn($r) => (float) $r['debit_total'] !== 0.0 || (float) $r['credit_total'] !== 0.0)->values();
        } else {
            $rows = $rows->values();
        }

        $totals = [
            'debit_total' => round($rows->sum('debit_total'), 2),
            'credit_total' => round($rows->sum('credit_total'), 2),
            'debit_balance' => round($rows->sum('debit_balance'), 2),
            'credit_balance' => round($rows->sum('credit_balance'), 2),
        ];
        $totals['difference'] = round($totals['debit_balance'] - $totals['credit_balance'], 2);

        return Inertia::render('Accounts/TrialBalance', [
            'from' => $from,
            'to' => $to,
            'includeZero' => $includeZero,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }
}
