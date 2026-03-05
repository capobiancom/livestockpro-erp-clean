<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Redirect based on role
        if ($user && $user->hasAnyRole(['Super Admin', 'admin'])) {
            if (!config('app.saas_mode', true)) {
                // In SaaS mode, send admins to the admin dashboard
                return redirect()->route('admin.settings.website.edit');
            } else {
                // In single-license mode, send all users to the profile page (hide admin dashboard
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }
        }

        // Farm owner: if no active subscription, force billing first.
        if ($user && $user->hasRole('farm owner')) {
            $farmId = $user->farm_id ?? null;
            if ($farmId) {

                if (!config('app.saas_mode', true)) {
                    // In SaaS mode, send admins to the admin dashboard
                    return redirect()->route('dashboard');
                }

                $activeSubscription = \App\Models\FarmSubscription::query()
                    ->where('farm_id', $farmId)
                    ->whereNull('cancelled_at')
                    ->whereDate('ends_on', '>=', now()->toDateString())
                    ->orderByDesc('ends_on')
                    ->first();

                if (!$activeSubscription) {
                    return redirect()->route('billing.index');
                }

                return redirect()->route('dashboard');
            }

            // Subscription is active: send farm owner to the app dashboard.
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Fallback (avoid redirecting to /dashboard which is farm-owner-only)
        return redirect()->intended(route('profile.edit', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
