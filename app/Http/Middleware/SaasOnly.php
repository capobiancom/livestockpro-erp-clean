<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SaasOnly
{
    /**
     * Block routes that should only exist in SaaS (multi-tenant) mode.
     * When SAAS_MODE=false (single-license mode), these routes should not be accessible.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.saas_mode', true)) {
            abort(404);
        }

        return $next($request);
    }
}
