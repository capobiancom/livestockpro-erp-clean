<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Super Admin', 'admin', 'manager', 'farm owner', 'herdsman', 'vet', 'driver'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign Super Admin to test user
        $user = User::where('email', 'test@example.com')->first();
        if ($user) {
            $user->assignRole('Super Admin');
            $user->assignRole('admin'); // Assign 'admin' role to Super Admin for route middleware
        }
    }
}
