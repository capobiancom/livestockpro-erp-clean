<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();
        return Inertia::render('Admin/Roles', compact('roles','permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);

        Role::create(['name' => $request->name]);

        return redirect()->back();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->back();
    }
}
