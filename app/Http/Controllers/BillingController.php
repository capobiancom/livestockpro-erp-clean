<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\FarmSubscription;
use App\Models\PaymentGatewayConfig;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $farmId = $user?->farm_id;

        $subscription = null;
        if ($farmId) {
            // Show the most recently created non-cancelled subscription record.
            // We create a new FarmSubscription on every "Continue" submit (invoice initiation),
            // so ordering by `created_at` ensures the latest selected billing_period is reflected
            // after refresh, even if multiple subscriptions share the same `ends_on` date.
            $subscription = FarmSubscription::query()
                ->where('farm_id', $farmId)
                ->whereNull('cancelled_at')
                ->orderByDesc('created_at')
                ->first();
        }

        $plans = SubscriptionPlan::query()
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
                ];
            });

        $enabledGateways = PaymentGatewayConfig::query()
            ->where("is_enabled", true)
            ->orderBy("gateway")
            ->pluck("gateway")
            ->values();

        $defaultGateway = PaymentGatewayConfig::query()
            ->where("is_default", true)
            ->value("gateway");

        return Inertia::render('Billing/Index', [
            // IMPORTANT:
            // `HandleInertiaRequests` already shares `subscription` globally as:
            //   subscription: { active, details, features }
            // The sidebar (AppLayout.vue) relies on `subscription.features` to decide which links to show.
            //
            // This controller previously overwrote the shared `subscription` prop with a FarmSubscription model,
            // which breaks the expected shape and causes feature-gating to behave incorrectly on /billing.
            //
            // So we avoid sending a conflicting `subscription` prop name from this page.
            'currentSubscription' => $subscription,
            'plans' => $plans,
            'enabledGateways' => $enabledGateways,
            'defaultGateway' => $defaultGateway,
        ]);
    }

    /**
     * Create invoice + payment initiation record.
     * Actual gateway API call will be implemented in PaymentGatewayController later.
     */
    public function subscribe(Request $request)
    {
        $user = $request->user();
        $farmId = $user?->farm_id;

        abort_unless($farmId, 422, 'Farm context missing.');

        // If a Super Admin inactivated the subscription (cancelled_at set), block billing submit/update.
        $hasCancelledSubscription = FarmSubscription::query()
            ->where('farm_id', $farmId)
            ->whereNotNull('cancelled_at')
            ->exists();

        abort_unless(!$hasCancelledSubscription, 403, 'Your subscription has been inactivated by an administrator. Please contact support.');
        $validated = $request->validate([
            'subscription_plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'billing_period' => ['required', 'string', 'in:monthly,yearly'],
            'gateway' => ['required', 'string', 'in:bkash,stripe,cod'],
        ]);

        $isEnabled = PaymentGatewayConfig::query()
            ->where('gateway', $validated['gateway'])
            ->where('is_enabled', true)
            ->exists();

        abort_unless($isEnabled, 422, 'Selected payment gateway is disabled.');

        $user = $request->user();
        $farmId = $user?->farm_id;

        abort_unless($farmId, 422, 'Farm context missing.');

        $plan = SubscriptionPlan::query()->findOrFail($validated['subscription_plan_id']);

        // Prevent re-subscribing to the same plan (or any plan with value <= current plan value)
        // when the farm already has an active subscription.
        $currentActiveSubscription = FarmSubscription::query()
            ->where('farm_id', $farmId)
            ->whereNull('cancelled_at')
            ->whereDate('ends_on', '>=', now()->toDateString())
            ->orderByDesc('created_at')
            ->first();

        if ($currentActiveSubscription) {
            $currentPlan = SubscriptionPlan::query()->find($currentActiveSubscription->subscription_plan_id);

            if ($currentPlan) {
                $currentValue = (int) $currentPlan->monthly_price_cents;
                $requestedValue = (int) $plan->monthly_price_cents;

                abort_unless(
                    $requestedValue > $currentValue,
                    422,
                    'You already have this plan (or a higher plan). Please choose a higher plan to upgrade.'
                );
            }
        }

        return DB::transaction(function () use ($farmId, $plan, $validated) {
            $startsOn = now()->toDateString();

            if ($validated['billing_period'] === 'monthly') {
                $endsOn = now()->addMonth()->toDateString();
                $subtotal = $plan->monthly_price_cents;
                $discount = 0;
                $total = $subtotal;
            } else {
                $endsOn = now()->addYear()->toDateString();
                $subtotal = $plan->monthly_price_cents * 12;
                $discount = (int) round($subtotal * ($plan->yearly_discount_percent / 100));
                $total = max(0, $subtotal - $discount);
            }

            $subscription = FarmSubscription::query()->create([
                'farm_id' => $farmId,
                'subscription_plan_id' => $plan->id,
                'billing_period' => $validated['billing_period'],
                'starts_on' => $startsOn,
                'ends_on' => $endsOn,
                'next_billing_on' => $endsOn,
            ]);

            $invoice = SubscriptionInvoice::query()->create([
                'farm_id' => $farmId,
                'farm_subscription_id' => $subscription->id,
                'invoice_number' => 'SUB-' . strtoupper(Str::random(10)),
                'invoice_date' => now()->toDateString(),
                'subtotal_cents' => $subtotal,
                'discount_cents' => $discount,
                'total_cents' => $total,
                'currency' => 'BDT',
                'status' => 'unpaid',
            ]);

            $checkDemoData = Farm::where('id', $farmId)->first();

            if ($checkDemoData && !$checkDemoData->demo_data_seeded) {
                $show_demo_seeding_popup = true;

                return redirect()
                    ->route('dashboard', [
                        'show_demo_seeding_popup' => $show_demo_seeding_popup,
                    ]);
            }

            // NOTE:
            // Do not create a SubscriptionPayment here.
            // Payment records are created/updated in PaymentGatewayController@initiate (single source of truth),
            // which also handles sandbox success and Stripe session creation.
            //
            // Redirect back to billing with invoice id so UI can initiate payment.
            return redirect()
                ->route('billing.index', [
                    'invoice' => $invoice->id,
                    'gateway' => $validated['gateway'],
                ])
                ->with('success', 'Subscription invoice created. Continue to payment.');
        });
    }
}
