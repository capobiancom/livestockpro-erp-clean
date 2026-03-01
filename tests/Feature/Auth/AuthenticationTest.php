<?php

namespace Tests\Feature\Auth;

use App\Models\Farm;
use App\Models\FarmSubscription;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.edit', absolute: false));
    }

    public function test_farm_owner_without_active_subscription_is_redirected_to_billing_after_login(): void
    {
        $farm = Farm::factory()->create();

        $user = User::factory()->create([
            'farm_id' => $farm->id,
        ]);

        // Ensure no active subscription exists for this farm.
        FarmSubscription::query()->where('farm_id', $farm->id)->delete();

        $this->seed(RoleSeeder::class);
        $user->assignRole('farm owner');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('billing.index', absolute: false));
    }

    public function test_farm_owner_with_active_subscription_is_redirected_to_admin_dashboard_after_login(): void
    {
        $farm = Farm::factory()->create();

        $user = User::factory()->create([
            'farm_id' => $farm->id,
        ]);

        $this->seed(RoleSeeder::class);
        $user->assignRole('farm owner');

        $this->seed(\Database\Seeders\SubscriptionCatalogSeeder::class);

        $planId = \App\Models\SubscriptionPlan::query()->value('id');

        FarmSubscription::query()->create([
            'farm_id' => $farm->id,
            'subscription_plan_id' => $planId,
            'billing_period' => 'monthly',
            'starts_on' => now()->toDateString(),
            'ends_on' => now()->addMonth()->toDateString(),
            'next_billing_on' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
