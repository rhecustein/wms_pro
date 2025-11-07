<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Load helpers
        require_once app_path('Helpers/settings.php');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Load settings to config at boot time
            $this->loadSettingsToConfig();
            
            // Share settings with all views
            $this->shareSettingsWithViews();
            
        } catch (\Exception $e) {
            // Silently fail during migration or if settings table doesn't exist
            logger()->warning('Settings could not be loaded: ' . $e->getMessage());
        }
    }

    /**
     * Load settings to Laravel config
     */
    private function loadSettingsToConfig(): void
    {
        $settings = Setting::getAllCached();
        
        // Update app config
        if (isset($settings['site_name'])) {
            Config::set('app.name', $settings['site_name']);
        }
        
        if (isset($settings['timezone'])) {
            Config::set('app.timezone', $settings['timezone']);
        }
        
        if (isset($settings['language'])) {
            Config::set('app.locale', $settings['language']);
        }
        
        // Update mail config from database
        $this->updateMailConfig($settings);
    }

    /**
     * Update mail configuration
     */
    private function updateMailConfig(array $settings): void
    {
        if (isset($settings['mail_driver'])) {
            Config::set('mail.default', $settings['mail_driver']);
        }
        
        if (isset($settings['mail_host'])) {
            Config::set('mail.mailers.smtp.host', $settings['mail_host']);
        }
        
        if (isset($settings['mail_port'])) {
            Config::set('mail.mailers.smtp.port', $settings['mail_port']);
        }
        
        if (isset($settings['mail_username'])) {
            Config::set('mail.mailers.smtp.username', $settings['mail_username']);
        }
        
        if (isset($settings['mail_password'])) {
            Config::set('mail.mailers.smtp.password', $settings['mail_password']);
        }
        
        if (isset($settings['mail_encryption'])) {
            Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
        }
        
        if (isset($settings['mail_from_address'])) {
            Config::set('mail.from.address', $settings['mail_from_address']);
        }
        
        if (isset($settings['mail_from_name'])) {
            Config::set('mail.from.name', $settings['mail_from_name']);
        }
    }

    /**
     * Share settings with all views
     */
    private function shareSettingsWithViews(): void
    {
        View::composer('*', function ($view) {
            $view->with([
                'siteName' => setting('site_name', 'WMS Pro'),
                'siteTagline' => setting('site_tagline', ''),
                'companyName' => setting('company_name', ''),
                'companyEmail' => setting('company_email', ''),
                'companyPhone' => setting('company_phone', ''),
                'themeMode' => setting('theme_mode', 'light'),
                'primaryColor' => setting('theme_primary_color', '#3b82f6'),
                'isEmailConfigured' => Setting::isEmailConfigured(),
            ]);
        });
    }
}