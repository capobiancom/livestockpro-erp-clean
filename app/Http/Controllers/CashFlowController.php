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

class CashFlowController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('cashFlow', JournalEntry::class);

        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->toDateString()
            : now()->startOfMonth()->toDateString();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->toDateString()
            : now()->toDateString();

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        // Cash Flow report (indirect method) for a period and posted entries only.
        // We approximate cash movements by using account classifications:
        // - Operating: income + expense (non-cash items not separated here)
        // - Investing: asset accounts (excluding cash/bank)
        // - Financing: liability + equity accounts
        //
        // NOTE: This is a best-effort report based on available data model (journal entries).
        // If you later add explicit cash/bank account tagging, we can refine to a true cash-only flow.
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

            // Net movement sign convention (positive = cash inflow, negative = cash outflow) by section:
            // - Income: credit - debit (inflow)
            // - Expense: -(debit - credit) (outflow)
            // - Asset: -(debit - credit) (asset increase consumes cash)
            // - Liability/Equity: credit - debit (increase provides cash)
            $movement = 0.0;

            if ($type === 'income') {
                $movement = $credit - $debit;
            } elseif ($type === 'expense') {
                $movement = -1 * ($debit - $credit);
            } elseif ($type === 'asset') {
                $movement = -1 * ($debit - $credit);
            } elseif (in_array($type, ['liability', 'equity'], true)) {
                $movement = $credit - $debit;
            }

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $type,
                'parent_id' => $acc->parent_id,
                'debit' => round($debit, 2),
                'credit' => round($credit, 2),
                'movement' => round($movement, 2),
            ];
        });

        $operating = $rows->whereIn('type', ['income', 'expense'])->values();
        $investing = $rows->where('type', 'asset')->values();
        $financing = $rows->whereIn('type', ['liability', 'equity'])->values();

        $totals = [
            'operating' => round($operating->sum('movement'), 2),
            'investing' => round($investing->sum('movement'), 2),
            'financing' => round($financing->sum('movement'), 2),
        ];
        $totals['net_change'] = round($totals['operating'] + $totals['investing'] + $totals['financing'], 2);

        return Inertia::render('Accounts/CashFlow', [
            'from' => $from,
            'to' => $to,
            'rows' => [
                'operating' => $operating,
                'investing' => $investing,
                'financing' => $financing,
            ],
            'totals' => $totals,
        ]);
    }
}
