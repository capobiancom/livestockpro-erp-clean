<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\InventoryItem;
use App\Models\Medicine;
use App\Models\Setting;
use App\Policies\AnimalPolicy;
use App\Policies\BreedPolicy;
use App\Policies\FarmPolicy;
use App\Policies\FeedingRecordPolicy;
use App\Policies\HerdPolicy;
use App\Policies\InventoryCategoryPolicy;
use App\Policies\InventoryItemPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SaleTransactionPolicy;
use App\Policies\StaffProfilePolicy;
use App\Policies\UserPolicy;
use App\Policies\VaccinationRecordPolicy;
use App\Policies\VaccineTypePolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Avoid Vite prefetch during unit tests (can consume lots of memory)
        if (! $this->app->runningUnitTests()) {
            Vite::prefetch(concurrency: 3);
        }

        Relation::morphMap([
            'inventory_item' => InventoryItem::class,
            'medicine_item' => Medicine::class,
        ]);

        // Register policies
        Gate::policy(\App\Models\Animal::class, AnimalPolicy::class);
        Gate::policy(\App\Models\Farm::class, FarmPolicy::class);
        Gate::policy(\App\Models\Breed::class, BreedPolicy::class);
        Gate::policy(\App\Models\Herd::class, HerdPolicy::class);
        Gate::policy(\App\Models\VaccineType::class, VaccineTypePolicy::class);
        Gate::policy(\App\Models\Purchase::class, PurchasePolicy::class);
        Gate::policy(\App\Models\InventoryItem::class, InventoryItemPolicy::class);
        Gate::policy(\App\Models\FeedingRecord::class, FeedingRecordPolicy::class);
        Gate::policy(\App\Models\VaccinationRecord::class, VaccinationRecordPolicy::class);
        Gate::policy(\App\Models\StaffProfile::class, StaffProfilePolicy::class);
        Gate::policy(\App\Models\Category::class, InventoryCategoryPolicy::class);
        Gate::policy(\App\Models\User::class, UserPolicy::class);
        Gate::policy(\App\Models\SaleTransaction::class, SaleTransactionPolicy::class);

        // Share settings with Inertia (skip if .env doesn't exist or installation in progress)
        Inertia::share('appSettings', function () {
            if (!file_exists(base_path('.env'))) {
                return $this->defaultSettings();
            }

            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::firstOrCreate([]);
                    return [
                        'app_title' => $settings->app_title,
                        'currency' => $settings->currency,
                        'currency_symbol' => match ($settings->currency) {
                            'USD' => '$',
                            'EUR' => '€',
                            'GBP' => '£',
                            'BDT' => '৳',
                            default => '$', // Default to dollar sign
                        },
                        'logo_path' => $settings->logo_path ? Storage::disk('public')->url($settings->logo_path) : null,
                        'timezone' => $settings->timezone,
                    ];
                }
            } catch (\Throwable $e) {
                // Silently fail if database is not initialized
            }

            return $this->defaultSettings();
        });

        // Set application timezone based on settings (skip if .env doesn't exist)
        if (file_exists(base_path('.env'))) {
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::firstOrCreate([]);
                    Config::set('app.timezone', $settings->timezone);
                    Carbon::setLocale(Config::get('app.locale'));
                }
            } catch (\Throwable $e) {
                // Silently fail if database is not initialized
            }
        }
    }

    /**
     * Get default settings when database is not available.
     */
    private function defaultSettings(): array
    {
        return [
            'app_title' => Config::get('app.name'),
            'currency' => 'USD',
            'currency_symbol' => '$',
            'logo_path' => null,
            'timezone' => Config::get('app.timezone'),
        ];
    }
}
