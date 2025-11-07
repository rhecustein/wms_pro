<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallationFinished
{
    public function handle($event)
    {
        // Jalankan seeder
        Artisan::call('db:seed');
        
        // Buat file marker installed
        File::put(storage_path('installed'), date('Y-m-d H:i:s'));
        
        // Clear cache
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        
        // Optimize
        Artisan::call('optimize');
    }
}