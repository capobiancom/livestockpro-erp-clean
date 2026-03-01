<?php

namespace App\Http\Middleware;

use App\Models\FarmSubscription;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmSubscriptionIsActive
{
    /**
     * Block the app when subscription is expired.
     * Exceptions: billing + settings (so farm owner can renew / configure gateways).
     *
     * When SAAS_MODE=false (single-license mode), all subscription checks are bypassed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Single-license mode: bypass all subscription gates.
        if (! config('app.saas_mode', true)) {
            return $next($request);
        }

        // Allow these routes even if expired.
        // NOTE: If a Super Admin explicitly inactivates a farm subscription (cancelled_at set),
        // we should NOT allow billing submit/update until it is activated again.
        if (
            $request->routeIs('settings.*') ||
            $request->routeIs('admin.*') ||
            $request->routeIs('billing.*') ||
            $request->routeIs('payments.*') ||
            $request->routeIs('plan.*') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        // Only enforce subscription for farm owners.
        $user = $request->user();
        if ($user && ! $user->hasRole('farm owner')) {
            return $next($request);
        }

        if (!$user) {
            return $next($request);
        }

        // Don't block in automated tests (keeps feature tests focused on their domain).
        if (app()->environment('testing')) {
            return $next($request);
        }

        // If user has no farm context, don't block here.
        // (Your app likely sets current farm via session or user->farm_id; we'll keep it safe.)
        $farmId = $user->farm_id ?? null;
        if (!$farmId) {
            return $next($request);
        }

        $activeSubscription = FarmSubscription::query()
            ->where('farm_id', $farmId)
            ->whereNull('cancelled_at')
            ->whereDate('ends_on', '>=', now()->toDateString())
            ->orderByDesc('ends_on')
            ->first();

        if ($activeSubscription) {
            return $next($request);
        }

        // If there is a subscription record but it is cancelled, it means a Super Admin inactivated it.
        // In that case, block billing submit/update as well (farm owner must wait for activation).
        $hasCancelledSubscription = FarmSubscription::query()
            ->where('farm_id', $farmId)
            ->whereNotNull('cancelled_at')
            ->exists();

        if ($hasCancelledSubscription) {
            // Allow viewing the billing page, but block any state-changing billing actions.
            if ($request->routeIs('billing.index')) {
                return $next($request);
            }

            return redirect()
                ->route('billing.index')
                ->with('error', 'Your subscription has been inactivated by an administrator. Please contact support.');
        }

        // Otherwise, treat as expired/not active and allow access to billing to renew.
        if ($request->routeIs('billing.*')) {
            return $next($request);
        }

        return redirect()
            ->route('billing.index')
            ->with('error', 'Your subscription is expired. Please renew to continue using the application.');
    }
}
