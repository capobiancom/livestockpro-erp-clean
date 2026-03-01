<?php

namespace App\Http\Controllers;

use App\Enums\CashTransactionDirection;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashTransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CashTransaction::class, 'cash_transaction');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cashTransactions = CashTransaction::query()
            ->with(['cashAccount', 'farm', 'user'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhere('reference_type', 'like', "%{$search}%");
            })
            ->when($request->input('direction'), function ($query, $direction) {
                $query->where('direction', $direction);
            })
            ->when($request->input('cash_account_id'), function ($query, $cashAccountId) {
                $query->where('cash_account_id', $cashAccountId);
            })
            ->when($request->input('date_from'), function ($query, $dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            })
            ->when($request->input('date_to'), function ($query, $dateTo) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            })
            ->where('farm_id', auth()->user()->farm_id)
            ->latest('transaction_date')
            ->paginate(15)
            ->withQueryString();

        $statistics = [
            'total_transactions' => CashTransaction::where('farm_id', auth()->user()->farm_id)->count(),
            'total_cash_in' => CashTransaction::where('farm_id', auth()->user()->farm_id)->where('direction', 'in')->sum('amount'),
            'total_cash_out' => CashTransaction::where('farm_id', auth()->user()->farm_id)->where('direction', 'out')->sum('amount'),
            'net_cash_flow' => CashTransaction::where('farm_id', auth()->user()->farm_id)->where('direction', 'in')->sum('amount') - CashTransaction::where('farm_id', auth()->user()->farm_id)->where('direction', 'out')->sum('amount'),
        ];

        $cashAccounts = CashAccount::where('farm_id', auth()->user()->farm_id)
            ->where('is_active', true)
            ->get(['id', 'name', 'type']);

        return Inertia::render('CashTransactions/Index', [
            'cashTransactions' => $cashTransactions,
            'filters' => $request->only(['search', 'direction', 'cash_account_id', 'date_from', 'date_to']),
            'directions' => CashTransactionDirection::cases(),
            'cashAccounts' => $cashAccounts,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $cashAccounts = CashAccount::where('farm_id', auth()->user()->farm_id)
            ->where('is_active', true)
            ->get(['id', 'name', 'type', 'current_balance']);

        $directionType = collect(CashTransactionDirection::cases())
            ->map(function ($type) {
                return ['name' => $type->name, 'value' => $type->value];
            });


        return Inertia::render('CashTransactions/Create', [
            'directions' => $directionType,
            'cashAccounts' => $cashAccounts,
            'preselectedAccountId' => $request->input('cash_account_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'direction' => 'required|in:in,out',
            'description' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|max:255',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['farm_id'] = auth()->user()->farm_id;

        // Get current balance and calculate balance after transaction
        $cashAccount = CashAccount::findOrFail($validated['cash_account_id']);
        if ($validated['direction'] === 'in') {
            $validated['balance_after'] = $cashAccount->current_balance + $validated['amount'];
        } else {
            $validated['balance_after'] = $cashAccount->current_balance - $validated['amount'];
        }

        CashTransaction::create($validated);

        return redirect()->route('cash-transactions.index')->with('success', 'Cash transaction recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CashTransaction $cashTransaction)
    {
        $cashTransaction->load(['cashAccount', 'farm', 'user']);

        return Inertia::render('CashTransactions/Show', [
            'cashTransaction' => $cashTransaction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashTransaction $cashTransaction)
    {
        $cashTransaction->load(['cashAccount']);

        $cashAccounts = CashAccount::where('farm_id', auth()->user()->farm_id)
            ->where('is_active', true)
            ->get(['id', 'name', 'type', 'current_balance']);

        $directionType = collect(CashTransactionDirection::cases())
            ->map(function ($type) {
                return ['name' => $type->name, 'value' => $type->value];
            });

        return Inertia::render('CashTransactions/Edit', [
            'cashTransaction' => $cashTransaction,
            'directions' => $directionType,
            'cashAccounts' => $cashAccounts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashTransaction $cashTransaction)
    {
        $validated = $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'direction' => 'required|in:in,out',
            'description' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|max:255',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
        ]);

        $cashTransaction->update($validated);

        return redirect()->route('cash-transactions.index')->with('success', 'Cash transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();

        return redirect()->route('cash-transactions.index')->with('success', 'Cash transaction deleted successfully.');
    }
}
