<?php

namespace App\Http\Middleware;

use App\Models\Farm;
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
        // Skip database queries during installation (no .env / no schema yet).
        // IMPORTANT: calling $request->user() will hit the `users` table, which may not exist yet.
        $isInstalling = (bool) preg_match('~^install~', $request->path()) || !file_exists(base_path('.env'));

        $user = $isInstalling ? null : $request->user();

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

        if (!$isInstalling && $user && $user->farm_id) {
            try {
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
            } catch (\Throwable $e) {
                // Silently fail if database is not initialized yet
            }
        }

        $logoPath = null;
        $currencySymbol = '$';
        $websiteCurrencySymbol = '$';

        if (!$isInstalling) {
            try {
                if (Schema::hasTable('settings')) {
                    $logoPath = Setting::first()?->logo_path ?? Setting::first()?->website_logo_path ?? null;
                    $currencySymbol = Setting::first()?->currency_symbol ?? '$';
                    $websiteCurrencySymbol = Setting::first()?->website_currency_symbol ?? '$';
                }
            } catch (\Throwable $e) {
                // Silently fail if database is not initialized yet
            }
        }

        $selectedFarmId = $user ? ($request->session()->get('farm_id') ?: $user->farm_id) : null;

        $availableFarms = [];
        if (!$isInstalling && $user) {
            // Current schema: farm owners are tied to a single farm via users.farm_id.
            // Super Admin/Admin may need to switch between farms.
            if ($user->hasRole('Super Admin') || $user->hasRole('admin')) {
                $availableFarms = Farm::query()
                    ->select(['id', 'name', 'code'])
                    ->orderBy('name')
                    ->get()
                    ->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'code' => $f->code])
                    ->toArray();
            } elseif ($user->farm_id) {
                $farm = Farm::query()->select(['id', 'name', 'code'])->find($user->farm_id);
                if ($farm) {
                    $availableFarms = [['id' => $farm->id, 'name' => $farm->name, 'code' => $farm->code]];
                }
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userResource,
            ],
            'farm_context' => [
                'selected_farm_id' => $selectedFarmId,
                'available_farms' => $availableFarms,
            ],

            // App mode flags
            'app_mode' => [
                // true = SaaS/multi-tenant (subscription enforced)
                // false = single-license (no subscription/billing UI)
                'saas_mode' => (bool) config('app.saas_mode', true),
                'single_license_mode' => ! (bool) config('app.saas_mode', true),
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
            'app_logo_path' => $logoPath,
            'app_currency_symbol' => $currencySymbol,
            'website_currency_symbol' => $websiteCurrencySymbol,

            // Public website-only settings (managed by Super Admin)
            'website_settings' => [
                'site_title' => Schema::hasTable('settings') ? Setting::first()?->site_title : null,
                'site_description' => Schema::hasTable('settings') ? Setting::first()?->site_description : null,
                'currency' => Schema::hasTable('settings') ? Setting::first()?->website_currency : null,
            ],
        ];
    }
}
