<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SubscriptionFeatureController extends Controller
{
    public function index()
    {
        $features = SubscriptionFeature::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/SubscriptionFeatures/Index', [
            'features' => $features,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SubscriptionFeatures/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:subscription_features,key'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['key'] = Str::slug($validated['key'], '_');

        SubscriptionFeature::create($validated);

        return redirect()
            ->route('admin.subscription-features.index')
            ->with('success', 'Subscription feature created.');
    }

    public function edit(SubscriptionFeature $subscriptionFeature)
    {
        return Inertia::render('Admin/SubscriptionFeatures/Edit', [
            'feature' => $subscriptionFeature,
        ]);
    }

    public function update(Request $request, SubscriptionFeature $subscriptionFeature)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['required', 'string', 'max:255', 'unique:subscription_features,key,' . $subscriptionFeature->id],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['key'] = Str::slug($validated['key'], '_');

        $subscriptionFeature->update($validated);

        return redirect()
            ->route('admin.subscription-features.index')
            ->with('success', 'Subscription feature updated.');
    }

    public function destroy(SubscriptionFeature $subscriptionFeature)
    {
        $subscriptionFeature->delete();

        return redirect()
            ->route('admin.subscription-features.index')
            ->with('success', 'Subscription feature deleted.');
    }
}
