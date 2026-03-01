<?php

namespace App\Http\Middleware;

use App\Models\FarmSubscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmHasFeature
{
    /**
     * Ensure the current farm's active subscription includes a given feature key.
     *
     * Usage: ->middleware('subscription.feature:reports')
     *
     * When SAAS_MODE=false (single-license mode), all feature gates are bypassed.
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        // Single-license mode: bypass all feature gates.
        if (! config('app.saas_mode', true)) {
            return $next($request);
        }

        // Allow billing/settings/logout regardless of feature gating
        if (
            $request->routeIs('billing.*') ||
            $request->routeIs('settings.*') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }


        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        // Don't block in automated tests unless explicitly desired.
        if (app()->environment('testing')) {
            return $next($request);
        }

        $farmId = $user->farm_id ?? null;
        if (!$farmId) {
            return $next($request);
        }

        $activeSubscription = FarmSubscription::query()
            ->with(['plan.enabledFeatures'])
            ->where('farm_id', $farmId)
            ->whereNull('cancelled_at')
            ->whereDate('ends_on', '>=', now()->toDateString())
            ->orderByDesc('ends_on')
            ->first();

        if (!$activeSubscription || !$activeSubscription->plan) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Your subscription does not include access to this feature. Please upgrade your plan.');
        }

        $enabledKeys = $activeSubscription->plan->enabledFeatures->pluck('key')->toArray();

        if (!in_array($featureKey, $enabledKeys, true)) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Your subscription does not include access to this feature. Please upgrade your plan.');
        }

        return $next($request);
    }
}
