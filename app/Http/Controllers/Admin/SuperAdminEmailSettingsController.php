<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSuperAdminEmailSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SuperAdminEmailSettingsController extends Controller
{
    public function edit(): Response
    {
        $settings = Setting::query()->firstOrCreate([]);

        return Inertia::render('Admin/Settings/SuperAdminEmail', [
            'settings' => [
                'super_admin_mail_mailer' => $settings->super_admin_mail_mailer,
                'super_admin_mail_host' => $settings->super_admin_mail_host,
                'super_admin_mail_port' => $settings->super_admin_mail_port,
                'super_admin_mail_username' => $settings->super_admin_mail_username,
                'super_admin_mail_encryption' => $settings->super_admin_mail_encryption,
                'super_admin_mail_from_address' => $settings->super_admin_mail_from_address,
                'super_admin_mail_from_name' => $settings->super_admin_mail_from_name,
            ],
        ]);
    }

    public function update(UpdateSuperAdminEmailSettingsRequest $request): RedirectResponse
    {
        $settings = Setting::query()->firstOrCreate([]);

        $validated = $request->validated();

        // Only update password if provided (avoid overwriting with empty string)
        if (!array_key_exists('super_admin_mail_password', $validated) || $validated['super_admin_mail_password'] === null || $validated['super_admin_mail_password'] === '') {
            unset($validated['super_admin_mail_password']);
        }

        $settings->fill($validated);
        $settings->save();

        return back()->with('success', 'Email settings updated.');
    }
}
