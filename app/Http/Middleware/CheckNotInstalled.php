<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah belum terinstall
        if (!file_exists(storage_path('installed'))) {
            return redirect()->route('installer.purchase');
        }

        return $next($request);
    }
}