<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminFarmsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin($request);

        $farms = Farm::query()
            ->withCount(['users'])
            ->with(['subscription.plan'])
            ->orderBy('name')
            ->get()
            ->map(fn(Farm $farm) => [
                'id' => $farm->id,
                'name' => $farm->name,
                'is_active' => (bool) $farm->subscription?->plan?->is_active,
                'users_count' => (int) $farm->users_count,
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
                'created_at' => optional($farm->created_at)->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Farms/Index', [
            'farms' => $farms,
        ]);
    }

    public function show(Request $request, Farm $farm)
    {
        $this->authorizeSuperAdmin($request);

        $farm->load([
            'subscription.plan',
            'users.roles',
        ]);

        $farmPayload = [
            'id' => $farm->id,
            'name' => $farm->name,
            'is_active' => (bool) $farm->subscription?->plan?->is_active,
            'created_at' => optional($farm->created_at)->toDateTimeString(),
            'updated_at' => optional($farm->updated_at)->toDateTimeString(),
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
        ];

        $users = $farm->users
            ->sortBy('name')
            ->values()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Never expose password hashes. "Credential info" here means login identifier + roles.
                'roles' => $user->roles->pluck('name')->values(),
                'created_at' => optional($user->created_at)->toDateTimeString(),
                'last_login_at' => optional($user->last_login_at ?? null)?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Farms/Show', [
            'farm' => $farmPayload,
            'users' => $users,
        ]);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole('Super Admin'), 403);
    }
}
