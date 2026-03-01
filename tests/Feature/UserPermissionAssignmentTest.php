<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPermissionAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_permissions_to_user()
    {
        $this->seed();

        $admin = User::where('email','test@example.com')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin);

        $user = User::factory()->create();
        $perm = Permission::create(['name' => 'custom.task']);

        $response = $this->post(route('admin.users.updatePermissions', $user), [
            'permissions' => ['custom.task']
        ]);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->hasPermissionTo('custom.task'));
    }

    public function test_user_with_direct_permission_can_perform_action()
    {
        $this->seed();
        $perm = Permission::create(['name' => 'animals.create']);
        $user = User::factory()->create();
        $user->givePermissionTo('animals.create');

        $this->actingAs($user);
        $response = $this->post(route('animals.store'), [
            'name' => 'New Animal'
        ]);

        $response->assertRedirect();
    }
}
