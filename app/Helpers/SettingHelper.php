<?php

// app/Helpers/SettingHelper.php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set setting value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return \App\Models\Setting
     */
    function setting_set($key, $value, $type = 'string')
    {
        return Setting::set($key, $value, $type);
    }
}

if (!function_exists('site_name')) {
    /**
     * Get site name
     * 
     * @return string
     */
    function site_name()
    {
        return setting('site_name', config('app.name', 'WMS Pro'));
    }
}

if (!function_exists('site_logo')) {
    /**
     * Get site logo URL
     * 
     * @param bool $dark
     * @return string|null
     */
    function site_logo($dark = false)
    {
        $key = $dark ? 'site_logo_dark' : 'site_logo';
        $logo = setting($key);
        
        if ($logo) {
            return \Storage::url($logo);
        }
        
        return null;
    }
}

if (!function_exists('site_favicon')) {
    /**
     * Get site favicon URL
     * 
     * @return string|null
     */
    function site_favicon()
    {
        $favicon = setting('site_favicon');
        
        if ($favicon) {
            return \Storage::url($favicon);
        }
        
        return null;
    }
}

if (!function_exists('company_name')) {
    /**
     * Get company name
     * 
     * @return string
     */
    function company_name()
    {
        return setting('company_name', 'Your Company');
    }
}

if (!function_exists('company_info')) {
    /**
     * Get company information as array
     * 
     * @return array
     */
    function company_info()
    {
        return [
            'name' => setting('company_name'),
            'email' => setting('company_email'),
            'phone' => setting('company_phone'),
            'whatsapp' => setting('company_whatsapp'),
            'address' => setting('company_address'),
            'city' => setting('company_city'),
            'state' => setting('company_state'),
            'country' => setting('company_country'),
            'postal_code' => setting('company_postal_code'),
            'website' => setting('company_website'),
            'tax_number' => setting('company_tax_number'),
        ];
    }
}

if (!function_exists('theme_color')) {
    /**
     * Get theme color
     * 
     * @param string $type (primary, secondary, sidebar)
     * @return string
     */
    function theme_color($type = 'primary')
    {
        return setting("theme_{$type}_color", '#3b82f6');
    }
}

if (!function_exists('social_links')) {
    /**
     * Get social media links
     * 
     * @return array
     */
    function social_links()
    {
        return [
            'facebook' => setting('social_facebook'),
            'twitter' => setting('social_twitter'),
            'instagram' => setting('social_instagram'),
            'linkedin' => setting('social_linkedin'),
            'youtube' => setting('social_youtube'),
        ];
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format number as currency
     * 
     * @param float $amount
     * @return string
     */
    function format_currency($amount)
    {
        $symbol = setting('currency_symbol', 'Rp');
        $decimals = 0;
        
        return $symbol . ' ' . number_format($amount, $decimals, ',', '.');
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date based on settings
     * 
     * @param string|\DateTime $date
     * @return string
     */
    function format_date($date)
    {
        if (!$date) return '';
        
        $format = setting('default_date_format', 'd/m/Y');
        
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format($format);
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format datetime based on settings
     * 
     * @param string|\DateTime $datetime
     * @return string
     */
    function format_datetime($datetime)
    {
        if (!$datetime) return '';
        
        $dateFormat = setting('default_date_format', 'd/m/Y');
        $timeFormat = setting('default_time_format', 'H:i');
        
        if (is_string($datetime)) {
            $datetime = new \DateTime($datetime);
        }
        
        return $datetime->format($dateFormat . ' ' . $timeFormat);
    }
}

if (!function_exists('is_feature_enabled')) {
    /**
     * Check if a feature is enabled
     * 
     * @param string $feature
     * @return bool
     */
    function is_feature_enabled($feature)
    {
        $key = "enable_{$feature}";
        return setting($key, 'false') === 'true';
    }
}

if (!function_exists('items_per_page')) {
    /**
     * Get items per page setting
     * 
     * @return int
     */
    function items_per_page()
    {
        return (int) setting('items_per_page', 15);
    }
}