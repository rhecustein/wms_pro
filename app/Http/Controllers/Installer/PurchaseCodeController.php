<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PurchaseCodeController extends Controller
{
    public function show()
    {
        return view('installer.purchase');
    }

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
            // Simpan purchase code ke file atau database
            file_put_contents(storage_path('app/purchase_code.txt'), $purchaseCode);
            
            session()->put('purchase_verified', true);
            
            return redirect()->route('LaravelInstaller::welcome')
                ->with('success', 'Purchase code verified successfully!');
        }

        return back()
            ->withErrors(['purchase_code' => 'Invalid purchase code or email.'])
            ->withInput();
    }

    protected function verifyPurchaseCode($code, $email)
    {
        // IMPORTANT: Ganti dengan Personal Token Envato Anda
        $envatoToken = env('ENVATO_TOKEN', 'ALSgDjqNozI4eGX8r1O0CLXvzrwyE1sR');
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $envatoToken,
                'User-Agent' => 'Purchase Code Verification',
            ])->get("https://api.envato.com/v3/market/author/sale", [
                'code' => $code,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Cek apakah email buyer sesuai
                if (isset($data['buyer']) && $data['buyer'] === $email) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            // Untuk development, bisa bypass
            if (env('APP_ENV') === 'local') {
                return true;
            }
            
            return false;
        }
    }
}