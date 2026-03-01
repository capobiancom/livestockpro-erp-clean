<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->with('permissions')->get();
        return Inertia::render('Admin/Permissions/Index', ['permissions' => $permissions, 'roles' => $roles]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:permissions,name']);
        Permission::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Permission created successfully!');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->back();
    }

    public function assignToRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        

        $role = \Spatie\Permission\Models\Role::where('name', $request->role)->firstOrFail();
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success','Permissions updated for role.');
    }
}
