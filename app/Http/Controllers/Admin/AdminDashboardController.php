<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeRoles($request);

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $receivableCents = SubscriptionInvoice::query()
            ->whereBetween('invoice_date', [$monthStart, $monthEnd])
            ->whereIn('status', ['unpaid', 'pending'])
            ->sum('total_cents');

        // "Collection" should reflect money actually received.
        // Keep this KPI aligned with Admin -> Collections, which counts only `succeeded` payments.
        $collectionCents = SubscriptionPayment::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'succeeded')
            ->sum('amount_cents');

        $farms = Farm::query()
            ->with(['subscription.plan'])
            ->withCount(['users'])
            ->orderBy('name')
            ->get()
            ->map(fn(Farm $farm) => [
                'id' => $farm->id,
                'name' => $farm->name,
                'is_active' => (bool) $farm->is_active,
                'users_count' => $farm->users_count,
                'subscription' => $farm->subscription ? [
                    'id' => $farm->subscription->id,
                    'plan' => $farm->subscription->plan ? [
                        'id' => $farm->subscription->plan->id,
                        'name' => $farm->subscription->plan->name,
                    ] : null,
                    'starts_on' => optional($farm->subscription->starts_on)->toDateString(),
                    'ends_on' => optional($farm->subscription->ends_on)->toDateString(),
                    'is_active' => $farm->subscription->isActive(),
                    'cancelled_at' => optional($farm->subscription->cancelled_at)->toDateTimeString(),
                ] : null,
            ]);

        return Inertia::render('Admin/Dashboard', [
            'kpis' => [
                'this_month_receivable_cents' => (int) $receivableCents,
                'this_month_collection_cents' => (int) $collectionCents,
                'month' => Carbon::now()->format('Y-m'),
            ],
            'farms' => $farms,
        ]);
    }

    private function authorizeRoles(Request $request): void
    {
        $user = $request->user();

        abort_unless($user && ($user->hasRole('Super Admin') || $user->hasRole('admin')), 403);
    }
}
