<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ChartOfAccountController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(ChartOfAccount::class, 'chart_of_account');
    }

    public function index()
    {
        $chartOfAccounts = ChartOfAccount::with('children')
            ->whereNull('parent_id')
            ->get()
            ->map(function ($account) {
                return $this->mapAccountToTree($account);
            });

        $flatAccounts = ChartOfAccount::all(['id', 'code', 'name']);

        return Inertia::render('ChartOfAccounts/Index', [
            'chartOfAccounts' => $chartOfAccounts,
            'flatAccounts' => $flatAccounts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chart_of_accounts')->where(function ($query) use ($request) {
                    return $query->where('farm_id', auth()->user()->farm_id);
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'is_system' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $chartOfAccount = new ChartOfAccount($request->all());
        $chartOfAccount->user_id = auth()->id();
        $chartOfAccount->farm_id = auth()->user()->farm_id;
        $chartOfAccount->save();

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account created successfully.');
    }

    public function update(Request $request, ChartOfAccount $chart_of_account)
    {
        $request->validate([
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('chart_of_accounts')->ignore($chart_of_account->id)->where(function ($query) use ($request) {
                    return $query->where('farm_id', auth()->user()->farm_id);
                }),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,income,expense'],
            'is_system' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $chart_of_account->update($request->all());

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(ChartOfAccount $chart_of_account)
    {
        $chart_of_account->delete();

        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted successfully.');
    }

    private function mapAccountToTree(ChartOfAccount $account)
    {
        return [
            'id' => $account->id,
            'code' => $account->code,
            'name' => $account->name,
            'type' => $account->type,
            'parent_id' => $account->parent_id,
            'is_system' => $account->is_system,
            'is_active' => $account->is_active,
            'children' => $account->children->map(function ($child) {
                return $this->mapAccountToTree($child);
            })->toArray(),
        ];
    }
}
