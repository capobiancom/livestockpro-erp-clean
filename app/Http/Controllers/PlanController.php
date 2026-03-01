<?php

namespace App\Http\Controllers;

use App\Models\FarmSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $farmId = $user?->farm_id;

        abort_unless($farmId, 422, 'Farm context missing.');

        $activeSubscription = FarmSubscription::query()
            ->with(['plan.enabledFeatures'])
            ->where('farm_id', $farmId)
            ->whereNull('cancelled_at')
            ->whereDate('ends_on', '>=', now()->toDateString())
            ->orderByDesc('ends_on')
            ->first();

        $currentPlan = null;
        $currentFeatures = [];
        $currentBillingPeriod = null;

        if ($activeSubscription?->plan) {
            $currentBillingPeriod = $activeSubscription->billing_period;

            $monthly = $activeSubscription->plan->monthly_price_cents;
            $yearlySubtotal = $monthly * 12;
            $discount = (int) round($yearlySubtotal * ($activeSubscription->plan->yearly_discount_percent / 100));
            $yearlyTotal = max(0, $yearlySubtotal - $discount);

            $currentPlan = [
                'id' => $activeSubscription->plan->id,
                'name' => $activeSubscription->plan->name,
                'slug' => $activeSubscription->plan->slug,
                'monthly_price_cents' => $monthly,
                'yearly_discount_percent' => $activeSubscription->plan->yearly_discount_percent,
                'yearly_price_cents' => $yearlyTotal,
                'is_active' => (bool) $activeSubscription->plan->is_active,
            ];

            $currentFeatures = $activeSubscription->plan->enabledFeatures
                ?->map(fn($f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'key' => $f->key,
                    'description' => $f->description,
                ])
                ?->values()
                ?->toArray() ?? [];
        }

        $allPlans = SubscriptionPlan::query()
            ->with(['enabledFeatures'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (SubscriptionPlan $plan) {
                $monthly = $plan->monthly_price_cents;
                $yearlySubtotal = $monthly * 12;
                $discount = (int) round($yearlySubtotal * ($plan->yearly_discount_percent / 100));
                $yearlyTotal = max(0, $yearlySubtotal - $discount);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'monthly_price_cents' => $monthly,
                    'yearly_discount_percent' => $plan->yearly_discount_percent,
                    'yearly_price_cents' => $yearlyTotal,
                    'features' => $plan->enabledFeatures
                        ?->map(fn($f) => [
                            'id' => $f->id,
                            'name' => $f->name,
                            'key' => $f->key,
                            'description' => $f->description,
                        ])
                        ?->values()
                        ?->toArray() ?? [],
                ];
            });

        return Inertia::render('Plan/Index', [
            'currentPlan' => $currentPlan,
            'currentFeatures' => $currentFeatures,
            'allPlans' => $allPlans,
            'subscriptionEndsOn' => $activeSubscription?->ends_on?->toDateString(),
            'currentBillingPeriod' => $currentBillingPeriod,
        ]);
    }
}
