<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmSwitchController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => ['required', 'integer', 'exists:farms,id'],
        ]);

        $user = $request->user();

        // Super Admin/Admin can switch to any farm (directory/admin context)
        // Farm owner can only switch to their own farm (current schema: users.farm_id)
        if ($user->hasRole('farm owner')) {
            if ((int) $user->farm_id !== (int) $validated['farm_id']) {
                abort(403, 'You are not allowed to switch to this farm.');
            }
        }

        // For other roles, allow switching only if they belong to that farm (if user has farm_id)
        if (!$user->hasRole('farm owner') && $user->farm_id) {
            if ((int) $user->farm_id !== (int) $validated['farm_id']) {
                abort(403, 'You are not allowed to switch to this farm.');
            }
        }

        // Ensure farm exists (exists rule already checked) and set session
        Farm::query()->whereKey($validated['farm_id'])->firstOrFail();

        $request->session()->put('farm_id', (int) $validated['farm_id']);

        return back()->with('success', 'Farm switched');
    }
}
