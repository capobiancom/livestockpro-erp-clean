<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfInstalled
{
    /**
     * Block access to /install routes once the application is already installed.
     * Installation is marked by the existence of storage/installed.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (file_exists(storage_path('installed'))) {
            return redirect('/')->with('info', 'LivestockPro ERP is already installed.');
        }

        return $next($request);
    }
}
