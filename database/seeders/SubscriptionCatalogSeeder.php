<?php

namespace Database\Seeders;

use App\Models\SubscriptionFeature;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlanFeature;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionCatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Features
            $features = [
                [
                    'key' => 'dashboard',
                    'name' => 'Farm main dashboard',
                    'description' => 'Manage farm dashboard.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'farmproductivity',
                    'name' => 'Farm productivity dashboard',
                    'description' => 'Manage farm productivity dashboard.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'animals',
                    'name' => 'Animal Management',
                    'description' => 'Manage animals, breeding, health events, and treatments.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'healths',
                    'name' => 'Healths Management',
                    'description' => 'Manage animals health.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'feedings',
                    'name' => 'Feedings Management',
                    'description' => 'Manage feeding.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'productions',
                    'name' => 'Milk Productions Management',
                    'description' => 'Milk Productions Management.',
                    'sort_order' => 10,
                ],
                [
                    'key' => 'inventory',
                    'name' => 'Inventory',
                    'description' => 'Track inventory items, purchases, stock movements, and alerts.',
                    'sort_order' => 20,
                ],
                [
                    'key' => 'sales',
                    'name' => 'Sales',
                    'description' => 'Create sales, invoices, and track customer payments.',
                    'sort_order' => 30,
                ],
                [
                    'key' => 'accounting',
                    'name' => 'Accounting',
                    'description' => 'Chart of accounts, journal entries, and financial statements.',
                    'sort_order' => 40,
                ],
                [
                    'key' => 'finance',
                    'name' => 'Finance Management',
                    'description' => 'Finanace',
                    'sort_order' => 40,
                ],
                [
                    'key' => 'customers',
                    'name' => 'Customers Management',
                    'description' => 'Farm Customers',
                    'sort_order' => 40,
                ],
                [
                    'key' => 'operation',
                    'name' => 'OperationManagement',
                    'description' => 'Farm operation like logistics',
                    'sort_order' => 40,
                ],
                [
                    'key' => 'hr',
                    'name' => 'HR & Payroll',
                    'description' => 'Staff, attendance, payroll items, and salary sheets.',
                    'sort_order' => 50,
                ],
                [
                    'key' => 'reports',
                    'name' => 'Reports',
                    'description' => 'Access operational and financial reports.',
                    'sort_order' => 60,
                ],
                [
                    'key' => 'invreports',
                    'name' => 'Inventory Reports',
                    'description' => 'Inventory reports information.',
                    'sort_order' => 60,
                ],
                [
                    'key' => 'billing',
                    'name' => 'Billing & Subscriptions',
                    'description' => 'Subscription management, invoices, and payments.',
                    'sort_order' => 70,
                ]
            ];

            $featureModelsByKey = [];
            foreach ($features as $feature) {
                $model = SubscriptionFeature::updateOrCreate(
                    ['key' => $feature['key']],
                    [
                        'name' => $feature['name'],
                        'description' => $feature['description'],
                        'is_active' => true,
                        'sort_order' => $feature['sort_order'],
                    ]
                );

                $featureModelsByKey[$model->key] = $model;
            }

            // Plans
            $plans = [
                [
                    'slug' => 'free',
                    'name' => 'Free',
                    'monthly_price_cents' => 0,
                    'yearly_discount_percent' => 0,
                    'sort_order' => 10,
                    'features' => [],
                ],
                [
                    'slug' => 'starter',
                    'name' => 'Starter',
                    'monthly_price_cents' => 9900,
                    'yearly_discount_percent' => 10,
                    'sort_order' => 20,
                    'features' => [
                        'animals' => true,
                        'healths' => true,
                        'feedings' => true,
                        'productions' => true,
                        'farmproductivity' => true,
                        'customers' => true,
                        'finance' => true,
                        'operation' => true,
                        'inventory' => true,
                        'sales' => true,
                        'hr' => true,
                        'billing' => true,
                    ],
                ],
                [
                    'slug' => 'pro',
                    'name' => 'Pro',
                    'monthly_price_cents' => 19900,
                    'yearly_discount_percent' => 15,
                    'sort_order' => 30,
                    'features' => [
                        'dashboard' => true,
                        'animals' => true,
                        'healths' => true,
                        'feedings' => true,
                        'productions' => true,
                        'farmproductivity' => true,
                        'customers' => true,
                        'finance' => true,
                        'operation' => true,
                        'inventory' => true,
                        'sales' => true,
                        'accounting' => true,
                        'hr' => true,
                        'reports' => true,
                        'invreports' => true,
                        'billing' => true,
                    ],
                ],
            ];

            foreach ($plans as $plan) {
                $planModel = SubscriptionPlan::updateOrCreate(
                    ['slug' => $plan['slug']],
                    [
                        'name' => $plan['name'],
                        'monthly_price_cents' => $plan['monthly_price_cents'],
                        'yearly_discount_percent' => $plan['yearly_discount_percent'],
                        'is_active' => true,
                        'sort_order' => $plan['sort_order'],
                    ]
                );

                foreach ($plan['features'] as $featureKey => $enabled) {
                    $featureModel = $featureModelsByKey[$featureKey] ?? null;
                    if (! $featureModel) {
                        continue;
                    }

                    SubscriptionPlanFeature::updateOrCreate(
                        [
                            'subscription_plan_id' => $planModel->id,
                            'subscription_feature_id' => $featureModel->id,
                        ],
                        [
                            'is_enabled' => (bool) $enabled,
                        ]
                    );
                }
            }
        });
    }
}
