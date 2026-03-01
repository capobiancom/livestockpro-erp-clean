<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendDemoEmailRequest;
use App\Mail\DemoPresentationMail;
use App\Models\DemoRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class DemoRequestAdminController extends Controller
{
    public function index(): Response
    {
        $requests = DemoRequest::query()
            ->latest()
            ->paginate(20)
            ->through(fn(DemoRequest $r) => [
                'id' => $r->id,
                'name' => $r->name,
                'email' => $r->email,
                'phone' => $r->phone,
                'company' => $r->company,
                'country' => $r->country,
                'preferred_date' => $r->preferred_date,
                'preferred_time' => $r->preferred_time,
                'timezone' => $r->timezone,
                'message' => $r->message,
                'status' => $r->status,
                'emailed_at' => optional($r->emailed_at)->toDateTimeString(),
                'scheduled_at' => optional($r->scheduled_at)->toDateTimeString(),
                'created_at' => optional($r->created_at)->toDateTimeString(),
            ]);

        return Inertia::render('Admin/DemoRequests/Index', [
            'requests' => $requests,
        ]);
    }

    public function sendEmail(DemoRequest $demoRequest, SendDemoEmailRequest $request): RedirectResponse
    {
        $settings = Setting::query()->firstOrCreate([]);

        // Apply Super Admin mail settings at runtime (best-effort).
        // Note: This affects the current request only.
        Config::set('mail.default', $settings->super_admin_mail_mailer ?: config('mail.default'));
        Config::set('mail.mailers.smtp.host', $settings->super_admin_mail_host ?: config('mail.mailers.smtp.host'));
        Config::set('mail.mailers.smtp.port', $settings->super_admin_mail_port ?: config('mail.mailers.smtp.port'));
        Config::set('mail.mailers.smtp.username', $settings->super_admin_mail_username ?: config('mail.mailers.smtp.username'));
        Config::set('mail.mailers.smtp.password', $settings->super_admin_mail_password ?: config('mail.mailers.smtp.password'));
        Config::set('mail.mailers.smtp.encryption', $settings->super_admin_mail_encryption ?: config('mail.mailers.smtp.encryption'));

        if ($settings->super_admin_mail_from_address) {
            Config::set('mail.from.address', $settings->super_admin_mail_from_address);
        }
        if ($settings->super_admin_mail_from_name) {
            Config::set('mail.from.name', $settings->super_admin_mail_from_name);
        }

        $validated = $request->validated();

        Mail::to($validated['to'])->send(
            new DemoPresentationMail(
                body: $validated['message'],
                scheduledAt: $validated['scheduled_at'] ?? null,
            ),
        );

        $demoRequest->forceFill([
            'status' => 'emailed',
            'emailed_at' => now(),
            'scheduled_at' => $validated['scheduled_at'] ?? $demoRequest->scheduled_at,
        ])->save();

        return back()->with('success', 'Demo email sent successfully.');
    }
}
