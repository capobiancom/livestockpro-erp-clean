<?php

namespace App\Http\Controllers;

use App\Enums\CashAccountType;
use App\Models\CashAccount;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashAccountController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CashAccount::class, 'cash_account');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cashAccounts = CashAccount::query()
            ->with(['chartOfAccount', 'farm'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%");
            })
            ->when($request->input('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->where('farm_id', auth()->user()->farm_id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statistics = [
            'total_accounts' => CashAccount::where('farm_id', auth()->user()->farm_id)->count(),
            'cash_accounts' => CashAccount::where('farm_id', auth()->user()->farm_id)->where('type', 'cash')->count(),
            'bank_accounts' => CashAccount::where('farm_id', auth()->user()->farm_id)->where('type', 'bank')->count(),
            'mobile_accounts' => CashAccount::where('farm_id', auth()->user()->farm_id)->where('type', 'mobile')->count(),
            'total_balance' => CashAccount::where('farm_id', auth()->user()->farm_id)->where('is_active', true)->sum('current_balance'),
        ];


        return Inertia::render('CashAccounts/Index', [
            'cashAccounts' => $cashAccounts,
            'filters' => $request->only(['search', 'type']),
            'accountTypes' => CashAccountType::cases(),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chartOfAccounts = ChartOfAccount::where('farm_id', auth()->user()->farm_id)
            ->where('type', 'asset')
            ->where('is_active', true)
            ->get(['id', 'name', 'code']);

        $cashAccountType = collect(CashAccountType::cases())
            ->map(function ($type) {
                return ['name' => $type->name, 'value' => $type->value];
            });

        return Inertia::render('CashAccounts/Create', [
            'accountTypes' => $cashAccountType,
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['farm_id'] = auth()->user()->farm_id;
        $validated['current_balance'] = $validated['opening_balance'] ?? 0;

        CashAccount::create($validated);

        return redirect()->route('cash-accounts.index')->with('success', 'Cash account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CashAccount $cashAccount)
    {
        $cashAccount->load(['chartOfAccount', 'farm', 'user', 'transactions' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('CashAccounts/Show', [
            'cashAccount' => $cashAccount,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CashAccount $cashAccount)
    {
        $cashAccount->load(['chartOfAccount', 'farm']);

        $chartOfAccounts = ChartOfAccount::where('farm_id', auth()->user()->farm_id)
            ->where('type', 'asset')
            ->where('is_active', true)
            ->get(['id', 'name', 'code']);

        $cashAccountType = collect(CashAccountType::cases())
            ->map(function ($type) {
                return ['name' => $type->name, 'value' => $type->value];
            });

        return Inertia::render('CashAccounts/Edit', [
            'cashAccount' => $cashAccount,
            'accountTypes' => $cashAccountType,
            'chartOfAccounts' => $chartOfAccounts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CashAccount $cashAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $cashAccount->update($validated);
        $cashAccount->updateBalance();

        return redirect()->route('cash-accounts.index')->with('success', 'Cash account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CashAccount $cashAccount)
    {
        if ($cashAccount->transactions()->count() > 0) {
            return redirect()->route('cash-accounts.index')->with('error', 'Cannot delete account with existing transactions.');
        }

        $cashAccount->delete();

        return redirect()->route('cash-accounts.index')->with('success', 'Cash account deleted successfully.');
    }
}
