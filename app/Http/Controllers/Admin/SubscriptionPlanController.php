<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/SubscriptionPlans/Index', [
            'plans' => $plans,
        ]);
    }

    public function create()
    {
        $features = SubscriptionFeature::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'key', 'description']);

        return Inertia::render('Admin/SubscriptionPlans/Create', [
            'features' => $features,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:subscription_plans,slug'],
            'monthly_price_cents' => ['required', 'integer', 'min:0'],
            'yearly_discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer', 'exists:subscription_features,id'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $plan = SubscriptionPlan::create($validated);

        $featureIds = collect($request->input('feature_ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $plan->features()->sync(Arr::mapWithKeys($featureIds, fn($id) => [$id => ['is_enabled' => true]]));

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created.');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->load(['features:id']);

        $features = SubscriptionFeature::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'key', 'description']);

        return Inertia::render('Admin/SubscriptionPlans/Edit', [
            'plan' => $subscriptionPlan,
            'features' => $features,
            'selectedFeatureIds' => $subscriptionPlan->features->pluck('id')->values(),
        ]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:subscription_plans,slug,' . $subscriptionPlan->id],
            'monthly_price_cents' => ['required', 'integer', 'min:0'],
            'yearly_discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'feature_ids' => ['nullable', 'array'],
            'feature_ids.*' => ['integer', 'exists:subscription_features,id'],
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);

        $subscriptionPlan->update($validated);

        $featureIds = collect($request->input('feature_ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $subscriptionPlan->features()->sync(Arr::mapWithKeys($featureIds, fn($id) => [$id => ['is_enabled' => true]]));

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted.');
    }
}
