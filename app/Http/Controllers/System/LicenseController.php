<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LicenseController extends Controller
{
    /**
     * Display license verification form
     */
    public function index()
    {
        $license = DB::table('settings')
            ->whereIn('key', ['purchase_code', 'buyer_email', 'license_type', 'installed_at', 'installed_domain'])
            ->get()
            ->keyBy('key');

        $isVerified = !empty($license['purchase_code']->value ?? null);

        return view('system.license.index', compact('license', 'isVerified'));
    }

    /**
     * Verify purchase code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string',
            'buyer_email' => 'required|email',
        ]);

        $purchaseCode = $request->input('purchase_code');
        $buyerEmail = $request->input('buyer_email');

        // Verify dengan Envato API
        $isValid = $this->verifyPurchaseCode($purchaseCode, $buyerEmail);

        if ($isValid) {
            // Save to settings
            $this->savePurchaseCode($purchaseCode, $buyerEmail);

            // Log activity
            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'purchase_code' => substr($purchaseCode, 0, 8) . '****',
                    'buyer_email' => $buyerEmail,
                ])
                ->log('Purchase code verified successfully');

            return redirect()
                ->route('system.license.index')
                ->with('success', 'Purchase code verified successfully! You can now use all features.');
        }

        return back()
            ->withErrors(['purchase_code' => 'Invalid purchase code or buyer email.'])
            ->withInput();
    }

    /**
     * Verify purchase code with Envato API
     */
    protected function verifyPurchaseCode($code, $email)
    {
        $envatoToken = config('services.envato.token');

        // Skip verification in local environment
        if (app()->environment('local') && empty($envatoToken)) {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $envatoToken,
                'User-Agent' => 'Purchase Code Verification',
            ])->get("https://api.envato.com/v3/market/author/sale", [
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Check if buyer email matches
                if (isset($data['buyer']) && strtolower($data['buyer']) === strtolower($email)) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error
            \Log::error('Purchase code verification failed', [
                'error' => $e->getMessage()
            ]);
            
            // Allow in development
            return app()->environment('local');
        }
    }

    /**
     * Save purchase code to settings
     */
    protected function savePurchaseCode($code, $email)
    {
        $domain = request()->getSchemeAndHttpHost();
        $now = now();
        
        $settings = [
            [
                'key' => 'purchase_code',
                'value' => $code,
                'type' => 'string',
                'group' => 'license',
                'description' => 'CodeCanyon purchase code',
                'is_public' => false,
                'is_editable' => false,
                'order' => 1,
                'updated_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'buyer_email',
                'value' => $email,
                'type' => 'email',
                'group' => 'license',
                'description' => 'Buyer email from Envato',
                'is_public' => false,
                'is_editable' => false,
                'order' => 2,
                'updated_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'license_type',
                'value' => 'regular',
                'type' => 'string',
                'group' => 'license',
                'description' => 'License type',
                'is_public' => false,
                'is_editable' => false,
                'order' => 3,
                'updated_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'installed_at',
                'value' => $now->toDateTimeString(),
                'type' => 'string',
                'group' => 'license',
                'description' => 'Installation date',
                'is_public' => false,
                'is_editable' => false,
                'order' => 4,
                'updated_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'installed_domain',
                'value' => $domain,
                'type' => 'url',
                'group' => 'license',
                'description' => 'Installed domain',
                'is_public' => false,
                'is_editable' => false,
                'order' => 5,
                'updated_by' => auth()->id(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}