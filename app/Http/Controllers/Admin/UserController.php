<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->paginate(20);
        $roles = Role::orderBy('name')->get();
        $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();

        // For "switch dashboard" UI: list farm owners with their farm.
        $farmOwners = User::query()
            ->whereHas('roles', fn($q) => $q->where('name', 'farm owner'))
            ->with(['farm:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'farm_id']);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
            'farmOwners' => $farmOwners,
        ]);
    }

    public function assignRoles()
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        return Inertia::render('Admin/AssignRoles', compact('users', 'roles'));
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->back()->with('success', 'Roles updated');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', 'Permissions updated');
    }
}
