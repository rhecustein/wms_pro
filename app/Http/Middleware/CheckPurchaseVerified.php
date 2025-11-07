<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckPurchaseVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check untuk route verify purchase
        if ($request->routeIs('system.license.*')) {
            return $next($request);
        }

        // Cek apakah purchase code sudah diverifikasi
        $purchaseCode = DB::table('settings')
            ->where('key', 'purchase_code')
            ->value('value');

        // Jika belum ada purchase code, redirect ke halaman verify
        if (empty($purchaseCode)) {
            return redirect()
                ->route('system.license.index')
                ->with('warning', 'Please verify your purchase code to continue using the application.');
        }

        return $next($request);
    }
}