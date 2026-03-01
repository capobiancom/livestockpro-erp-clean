<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoRequestRequest;
use App\Models\DemoRequest;
use Illuminate\Http\RedirectResponse;

class DemoRequestController extends Controller
{
    public function store(StoreDemoRequestRequest $request): RedirectResponse
    {
        DemoRequest::create([
            ...$request->validated(),
            'status' => 'new',
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'referer' => (string) $request->headers->get('referer'),
            ],
        ]);

        return back()->with('success', 'Thanks! Your demo request has been received. We will contact you shortly.');
    }
}
