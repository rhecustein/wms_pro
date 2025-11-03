<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load helper files
        $helpers = [
            'SettingHelper',
            // Add more helpers here
        ];

        foreach ($helpers as $helper) {
            $helperPath = app_path("Helpers/{$helper}.php");
            
            if (file_exists($helperPath)) {
                require_once $helperPath;
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}