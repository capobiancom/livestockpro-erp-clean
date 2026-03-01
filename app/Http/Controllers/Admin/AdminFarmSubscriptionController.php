<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\FarmSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminFarmSubscriptionController extends Controller
{
    public function toggle(Request $request, Farm $farm): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->hasRole('Super Admin') || $user->hasRole('admin')), 403);

        // Toggle the same "current" subscription record that the UI shows (Farm::subscription).
        // This avoids toggling an older subscription while a newer one remains active.
        $subscription = $farm->subscription;

        if (!$subscription) {
            return back()->with('error', 'This farm has no subscription to toggle.');
        }

        // Toggle by setting/unsetting cancelled_at.
        // When cancelled_at is set, subscription becomes inactive immediately (see FarmSubscription::isActive()).
        if ($subscription->cancelled_at) {
            $subscription->update(['cancelled_at' => null]);
            return back()->with('success', 'Subscription activated.');
        }

        $subscription->update(['cancelled_at' => now()]);
        return back()->with('success', 'Subscription inactivated.');
    }
}
