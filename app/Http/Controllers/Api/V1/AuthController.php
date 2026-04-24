<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Models\FarmSubscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('email', $request->string('email'))
            ->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke existing tokens for this device name (optional but helps keep token list clean)
        $user->tokens()
            ->where('name', $request->string('device_name'))
            ->delete();

        $token = $user->createToken(
            name: $request->string('device_name'),
            abilities: ['*'],
        );

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Return the authenticated user's full context for external services
     * (e.g. the Node/Vacaliza backend) so they can make authorization
     * decisions (role checks, farm scoping, feature gates) without needing
     * to query the Laravel database directly.
     *
     * Shape:
     *  {
     *    user: { id, name, email, roles[], farm_id, is_super_admin },
     *    farm: { id, name, country } | null,
     *    subscription: { active, plan_key, ends_on, features[] } | null,
     *    app_mode: { saas_mode, single_license_mode }
     *  }
     */
    public function me(): JsonResponse
    {
        /** @var User|null $user */
        $user = request()->user();

        if (! $user) {
            // Should not normally be reached because this route is behind
            // auth:sanctum, but keep a defensive 401 just in case.
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Eager-load roles to avoid N+1 when calling getRoleNames() / hasRole().
        $user->loadMissing('roles');

        $isSuperAdmin = $user->hasRole('Super Admin');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->values()->all(),
                'farm_id' => $user->farm_id,
                'is_super_admin' => $isSuperAdmin,
            ],
            'farm' => $this->buildFarmPayload($user),
            'subscription' => $this->buildSubscriptionPayload($user),
            'app_mode' => [
                'saas_mode' => (bool) config('app.saas_mode', true),
                'single_license_mode' => ! (bool) config('app.saas_mode', true),
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json([
            'message' => 'Logged out.',
        ]);
    }

    /**
     * Build the farm payload for the authenticated user.
     *
     * Returns null when the user has no farm_id (e.g. a Super Admin that
     * hasn't selected/belongs to a specific farm).
     */
    private function buildFarmPayload(User $user): ?array
    {
        if (! $user->farm_id) {
            return null;
        }

        $farm = $user->farm()->first();

        if (! $farm) {
            return null;
        }

        // `country` is not guaranteed to exist on the farms table in every
        // deployment, so fall back to null if the attribute is missing.
        $country = null;
        if (array_key_exists('country', $farm->getAttributes())) {
            $country = $farm->getAttribute('country');
        }

        return [
            'id' => $farm->id,
            'name' => $farm->name,
            'country' => $country,
        ];
    }

    /**
     * Build the subscription payload for the authenticated user's farm.
     *
     * Mirrors the logic used in HandleInertiaRequests so web + API stay
     * consistent:
     *   - active = not cancelled and ends_on >= today
     *   - plan_key = plan slug (closest thing to a machine-safe key)
     *   - features = enabled feature keys for the plan
     *
     * Returns null when the user has no farm or no matching subscription.
     */
    private function buildSubscriptionPayload(User $user): ?array
    {
        if (! $user->farm_id) {
            return null;
        }

        $activeSubscription = FarmSubscription::query()
            ->with(['plan.enabledFeatures'])
            ->where('farm_id', $user->farm_id)
            ->whereNull('cancelled_at')
            ->whereDate('ends_on', '>=', now()->toDateString())
            ->orderByDesc('ends_on')
            ->first();

        if (! $activeSubscription) {
            return null;
        }

        $plan = $activeSubscription->plan;

        $features = $plan
            ? $plan->enabledFeatures->pluck('key')->values()->all()
            : [];

        return [
            'active' => true,
            'plan_key' => $plan?->slug ?? $plan?->name,
            'ends_on' => $activeSubscription->ends_on?->toDateString(),
            'features' => $features,
        ];
    }
}
