<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\FarmNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminFarmNotificationController extends Controller
{
    public function send(Request $request, Farm $farm): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->hasRole('Super Admin') || $user->hasRole('admin')), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        FarmNotification::create([
            'farm_id' => $farm->id,
            'sent_by_user_id' => $user->id,
            'message' => $data['message'],
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Notification sent.');
    }
}
