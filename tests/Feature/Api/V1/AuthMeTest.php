<?php

namespace Tests\Feature\Api\V1;

use App\Models\Farm;
use App\Models\FarmSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SubscriptionCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }

    public function test_farm_owner_receives_full_payload_with_subscription_and_features(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(SubscriptionCatalogSeeder::class);

        // Ensure SaaS mode so `app_mode.saas_mode` is true.
        config(['app.saas_mode' => true]);

        $farm = Farm::factory()->create([
            'name' => 'Hacienda X',
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'farm_id' => $farm->id,
        ]);
        $user->assignRole('farm owner');

        // Attach an active subscription on the "pro" plan (seeded).
        $plan = SubscriptionPlan::where('slug', 'pro')->firstOrFail();

        FarmSubscription::create([
            'farm_id' => $farm->id,
            'subscription_plan_id' => $plan->id,
            'billing_period' => 'monthly',
            'starts_on' => now()->subMonth()->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me')->assertOk();

        // user block
        $response->assertJsonPath('user.id', $user->id);
        $response->assertJsonPath('user.name', $user->name);
        $response->assertJsonPath('user.email', $user->email);
        $response->assertJsonPath('user.farm_id', $farm->id);
        $response->assertJsonPath('user.is_super_admin', false);
        $this->assertContains('farm owner', $response->json('user.roles'));

        // farm block
        $response->assertJsonPath('farm.id', $farm->id);
        $response->assertJsonPath('farm.name', 'Hacienda X');
        $this->assertArrayHasKey('country', $response->json('farm'));

        // subscription block
        $response->assertJsonPath('subscription.active', true);
        $response->assertJsonPath('subscription.plan_key', 'pro');
        $this->assertNotNull($response->json('subscription.ends_on'));

        $features = $response->json('subscription.features');
        $this->assertIsArray($features);
        // The seeded "pro" plan enables these keys.
        $this->assertContains('animals', $features);
        $this->assertContains('healths', $features);
        $this->assertContains('finance', $features);

        // app_mode
        $response->assertJsonPath('app_mode.saas_mode', true);
        $response->assertJsonPath('app_mode.single_license_mode', false);
    }

    public function test_super_admin_has_is_super_admin_true_and_no_farm_when_unassigned(): void
    {
        $this->seed(RoleSeeder::class);

        /** @var User $user */
        $user = User::factory()->create([
            'farm_id' => null,
        ]);
        $user->assignRole('Super Admin');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me')->assertOk();

        $response->assertJsonPath('user.is_super_admin', true);
        $response->assertJsonPath('user.farm_id', null);
        $this->assertContains('Super Admin', $response->json('user.roles'));

        // No farm_id -> null farm / null subscription.
        $response->assertJsonPath('farm', null);
        $response->assertJsonPath('subscription', null);
    }

    public function test_single_license_mode_is_reflected_in_app_mode(): void
    {
        $this->seed(RoleSeeder::class);

        config(['app.saas_mode' => false]);

        /** @var User $user */
        $user = User::factory()->create(['farm_id' => null]);
        $user->assignRole('farm owner');

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('app_mode.saas_mode', false)
            ->assertJsonPath('app_mode.single_license_mode', true)
            ->assertJsonPath('subscription', null);
    }
}
