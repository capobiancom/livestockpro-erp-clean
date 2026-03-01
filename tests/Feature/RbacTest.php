<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Animal;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_has_permissions_after_seeding()
    {
        $this->seed(); // runs DatabaseSeeder which includes roles & permissions

        $admin = User::where('email','test@example.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));

        $this->assertTrue($admin->hasPermissionTo('animals.view'));
    }

    public function test_user_with_role_can_update_animal()
    {
        $this->seed();
        $role = Role::create(['name' => 'tester']);
        $perm = Permission::firstOrCreate(['name' => 'animals.update']);
        $role->givePermissionTo($perm);

        $user = User::factory()->create();
        $user->assignRole($role);

        $animal = Animal::factory()->create();

        $this->assertTrue($user->can('animals.update'));

        $this->actingAs($user);
        $response = $this->patch(route('animals.update', $animal), [
            'name' => 'Updated',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('animals', ['id' => $animal->id, 'name' => 'Updated']);
    }

    public function test_user_without_permission_cannot_update_animal()
    {
        $this->seed();
        $user = User::factory()->create();
        $animal = Animal::factory()->create();

        $this->actingAs($user);
        $response = $this->patch(route('animals.update', $animal), [
            'name' => 'ShouldNotUpdate',
        ]);

        $response->assertStatus(403);
    }
}
