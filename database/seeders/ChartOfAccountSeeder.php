<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        $farm = \App\Models\Farm::first();

        if (! $user) {
            $user = \App\Models\User::factory()->create();
        }

        if (! $farm) {
            $farm = \App\Models\Farm::factory()->forUser($user)->create();
        }

        $accounts = [
            // Assets
            [
                'code' => '1000',
                'name' => 'Assets',
                'type' => \App\Enums\ChartOfAccountType::Asset,
                'is_system' => true,
                'children' => [
                    ['code' => '1010', 'name' => 'Current Assets', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1020', 'name' => 'Cash', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1030', 'name' => 'Accounts Receivable', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1040', 'name' => 'Inventory', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1050', 'name' => 'Fixed Assets', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1060', 'name' => 'Land', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1070', 'name' => 'Buildings', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                    ['code' => '1080', 'name' => 'Equipment', 'type' => \App\Enums\ChartOfAccountType::Asset, 'is_system' => true],
                ],
            ],
            // Liabilities
            [
                'code' => '2000',
                'name' => 'Liabilities',
                'type' => \App\Enums\ChartOfAccountType::Liability,
                'is_system' => true,
                'children' => [
                    ['code' => '2010', 'name' => 'Current Liabilities', 'type' => \App\Enums\ChartOfAccountType::Liability, 'is_system' => true],
                    ['code' => '2001', 'name' => 'Accounts Payable', 'type' => \App\Enums\ChartOfAccountType::Liability, 'is_system' => true],
                    ['code' => '2030', 'name' => 'Salaries Payable', 'type' => \App\Enums\ChartOfAccountType::Liability, 'is_system' => true],
                    ['code' => '2040', 'name' => 'Long-Term Liabilities', 'type' => \App\Enums\ChartOfAccountType::Liability, 'is_system' => true],
                    ['code' => '2050', 'name' => 'Bank Loans', 'type' => \App\Enums\ChartOfAccountType::Liability, 'is_system' => true],
                ],
            ],
            // Equity
            [
                'code' => '3000',
                'name' => 'Equity',
                'type' => \App\Enums\ChartOfAccountType::Equity,
                'is_system' => true,
                'children' => [
                    ['code' => '3010', 'name' => 'Owner\'s Equity', 'type' => \App\Enums\ChartOfAccountType::Equity, 'is_system' => true],
                    ['code' => '3020', 'name' => 'Retained Earnings', 'type' => \App\Enums\ChartOfAccountType::Equity, 'is_system' => true],
                ],
            ],
            // Income
            [
                'code' => '4000',
                'name' => 'Income',
                'type' => \App\Enums\ChartOfAccountType::Income,
                'is_system' => true,
                'children' => [
                    ['code' => '4010', 'name' => 'Sales Revenue', 'type' => \App\Enums\ChartOfAccountType::Income, 'is_system' => true],
                    ['code' => '4020', 'name' => 'Other Income', 'type' => \App\Enums\ChartOfAccountType::Income, 'is_system' => true],
                ],
            ],
            // Expenses
            [
                'code' => '5000',
                'name' => 'Expenses',
                'type' => \App\Enums\ChartOfAccountType::Expense,
                'is_system' => true,
                'children' => [
                    ['code' => '5010', 'name' => 'Cost of Goods Sold', 'type' => \App\Enums\ChartOfAccountType::Expense, 'is_system' => true],
                    ['code' => '5020', 'name' => 'Salaries Expense', 'type' => \App\Enums\ChartOfAccountType::Expense, 'is_system' => true],
                    ['code' => '5030', 'name' => 'Rent Expense', 'type' => \App\Enums\ChartOfAccountType::Expense, 'is_system' => true],
                    ['code' => '5040', 'name' => 'Utilities Expense', 'type' => \App\Enums\ChartOfAccountType::Expense, 'is_system' => true],
                    ['code' => '5050', 'name' => 'Depreciation Expense', 'type' => \App\Enums\ChartOfAccountType::Expense, 'is_system' => true],
                ],
            ],
        ];

        foreach ($accounts as $accountData) {
            $parent = \App\Models\ChartOfAccount::firstOrCreate(
                [
                    'farm_id' => $farm->id,
                    'code' => $accountData['code'],
                ],
                [
                    'user_id' => $user->id,
                    'name' => $accountData['name'],
                    'type' => $accountData['type'],
                    'is_system' => $accountData['is_system'],
                    'is_active' => true,
                ]
            );

            if (isset($accountData['children'])) {
                foreach ($accountData['children'] as $childData) {
                    \App\Models\ChartOfAccount::firstOrCreate(
                        [
                            'farm_id' => $farm->id,
                            'code' => $childData['code'],
                        ],
                        [
                            'user_id' => $user->id,
                            'name' => $childData['name'],
                            'type' => $childData['type'],
                            'parent_id' => $parent->id,
                            'is_system' => $childData['is_system'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
