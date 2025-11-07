<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah sudah terinstall
        if (file_exists(storage_path('installed'))) {
            return redirect('/');
        }

        return $next($request);
    }
}