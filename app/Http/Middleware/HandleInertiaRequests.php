<?php

namespace App\Http\Middleware;

use App\Models\FarmSubscription;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $userResource = null;
        if ($user) {
            $userResource = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                'roles' => $user->getRoleNames(),
                'farm_id' => $user->farm_id,
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ];
        }


        $subscription = null;
        $enabledFeatureKeys = [];

        if ($user && $user->farm_id) {
            $activeSubscription = FarmSubscription::query()
                ->with(['plan.enabledFeatures'])
                ->where('farm_id', $user->farm_id)
                ->whereNull('cancelled_at')
                ->whereDate('ends_on', '>=', now()->toDateString())
                ->orderByDesc('ends_on')
                ->first();

            if ($activeSubscription) {
                $subscription = [
                    'id' => $activeSubscription->id,
                    'plan' => [
                        'id' => $activeSubscription->plan?->id,
                        'name' => $activeSubscription->plan?->name,
                        'slug' => $activeSubscription->plan?->slug,
                    ],
                    'starts_on' => $activeSubscription->starts_on?->toDateString(),
                    'ends_on' => $activeSubscription->ends_on?->toDateString(),
                ];

                $enabledFeatureKeys = $activeSubscription->plan
                    ?->enabledFeatures
                    ?->pluck('key')
                    ?->values()
                    ?->toArray() ?? [];
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userResource,
            ],
            'subscription' => [
                'active' => (bool) $subscription,
                'details' => $subscription,
                'features' => $enabledFeatureKeys,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'info' => fn() => $request->session()->get('info'),
            ],
            'app_logo_path' => Schema::hasTable('settings') ? Setting::first()?->logo_path : null,
            'app_currency_symbol' => Schema::hasTable('settings') ? (Setting::first()?->currency_symbol ?? '$') : '$',
            'website_currency_symbol' => Schema::hasTable('settings') ? (Setting::first()?->website_currency_symbol ?? '$') : '$',

            // Public website-only settings (managed by Super Admin)
            'website_settings' => [
                'site_title' => Schema::hasTable('settings') ? Setting::first()?->site_title : null,
                'site_description' => Schema::hasTable('settings') ? Setting::first()?->site_description : null,
                'currency' => Schema::hasTable('settings') ? Setting::first()?->website_currency : null,
            ],
        ];
    }
}
