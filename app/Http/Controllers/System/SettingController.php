<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    // Group configuration
    private array $groupLabels = [
        'identity' => 'Identity Settings',
        'company' => 'Company Information',
        'appearance' => 'Appearance Settings',
        'social' => 'Social Media',
        'email' => 'Email Configuration',
        'email_notifications' => 'Email Notifications',
        'email_templates' => 'Email Templates',
        'warehouse' => 'Warehouse Settings',
        'inventory' => 'Inventory Settings',
        'notifications' => 'Notification Settings',
        'report' => 'Report Settings',
        'security' => 'Security Settings',
        'system' => 'System Configuration',
        'general' => 'General Settings',
    ];

    private array $groupIcons = [
        'identity' => 'fa-id-card',
        'company' => 'fa-building',
        'appearance' => 'fa-palette',
        'social' => 'fa-share-alt',
        'email' => 'fa-envelope-open-text',
        'email_notifications' => 'fa-bell',
        'email_templates' => 'fa-file-alt',
        'warehouse' => 'fa-warehouse',
        'inventory' => 'fa-boxes',
        'notifications' => 'fa-bell',
        'report' => 'fa-chart-bar',
        'security' => 'fa-shield-alt',
        'system' => 'fa-server',
        'general' => 'fa-cog',
    ];

    /**
     * Display all settings grouped
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        
        return view('system.settings.index', [
            'settings' => $settings,
            'groupLabels' => $this->groupLabels,
            'groupIcons' => $this->groupIcons,
        ]);
    }

    /**
     * Show settings for specific group
     */
    public function show(string $group)
    {
        $settings = Setting::where('group', $group)
            ->orderBy('order')
            ->get();

        if ($settings->isEmpty()) {
            return redirect()
                ->route('system.settings.index')
                ->with('error', 'Settings group not found');
        }

        return view('system.settings.show', [
            'group' => $group,
            'settings' => $settings,
            'groupLabels' => $this->groupLabels,
            'groupIcons' => $this->groupIcons,
        ]);
    }

    /**
     * Update settings for specific group
     */
    public function updateGroup(Request $request, string $group)
    {
        try {
            DB::beginTransaction();

            $settings = Setting::where('group', $group)
                ->where('is_editable', true)
                ->get();

            foreach ($settings as $setting) {
                $value = $this->processSettingValue($request, $setting);
                
                if ($value !== null) {
                    $setting->update([
                        'value' => $value,
                        'updated_by' => auth()->id(),
                    ]);
                }
            }

            // Update mail configuration if email group
            if ($group === 'email') {
                $this->updateMailConfig();
            }

            DB::commit();
            Cache::forget('settings');

            return redirect()
                ->route('system.settings.show', $group)
                ->with('success', 'Settings updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Update mail configuration
            $this->updateMailConfig();

            $siteName = Setting::get('site_name', 'WMS Pro');
            $testEmail = $request->test_email;

            // Send test email
            Mail::raw(
                "This is a test email from {$siteName}.\n\nYour email configuration is working correctly!\n\nSent at: " . now()->format('Y-m-d H:i:s'),
                function ($message) use ($testEmail, $siteName) {
                    $message->to($testEmail)
                        ->subject("Test Email - {$siteName}");
                }
            );

            return redirect()
                ->back()
                ->with('success', 'Test email sent successfully to ' . $testEmail);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Update mail configuration from settings
     */
    private function updateMailConfig(): void
    {
        $mailSettings = Setting::where('group', 'email')->pluck('value', 'key');

        Config::set('mail.default', $mailSettings->get('mail_driver', 'smtp'));
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $mailSettings->get('mail_host'));
        Config::set('mail.mailers.smtp.port', $mailSettings->get('mail_port'));
        Config::set('mail.mailers.smtp.username', $mailSettings->get('mail_username'));
        Config::set('mail.mailers.smtp.password', $mailSettings->get('mail_password'));
        Config::set('mail.mailers.smtp.encryption', $mailSettings->get('mail_encryption'));
        Config::set('mail.from.address', $mailSettings->get('mail_from_address'));
        Config::set('mail.from.name', $mailSettings->get('mail_from_name'));
    }

    /**
     * Upload file for setting
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:5120',
            'setting_key' => 'required|exists:settings,key',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $setting = Setting::where('key', $request->setting_key)->firstOrFail();
            
            // Validate file type for images
            if ($setting->type === 'image') {
                $request->validate([
                    'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);
            }

            // Delete old file if exists
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            // Store new file
            $path = $request->file('file')->store('settings', 'public');

            // Update setting
            $setting->update([
                'value' => $path,
                'updated_by' => auth()->id(),
            ]);

            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'path' => $path,
                'url' => Storage::url($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file from setting
     */
    public function deleteFile(string $key)
    {
        try {
            $setting = Setting::where('key', $key)->firstOrFail();

            if (!$setting->is_editable) {
                return redirect()
                    ->back()
                    ->with('error', 'This setting cannot be modified');
            }

            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            $setting->update([
                'value' => null,
                'updated_by' => auth()->id(),
            ]);

            Cache::forget('settings');

            return redirect()
                ->back()
                ->with('success', 'File deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache()
    {
        try {
            Cache::forget('settings');
            Cache::flush();

            return redirect()
                ->back()
                ->with('success', 'Cache cleared successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Export settings to JSON
     */
    public function export()
    {
        try {
            $settings = Setting::all()->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'description' => $setting->description,
                ];
            });

            $filename = 'settings_export_' . now()->format('Y-m-d_His') . '.json';

            return response()->json($settings, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings from JSON
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }

            DB::beginTransaction();

            $imported = 0;
            foreach ($data as $item) {
                $setting = Setting::where('key', $item['key'])->first();
                
                if ($setting && $setting->is_editable) {
                    $setting->update([
                        'value' => $item['value'],
                        'updated_by' => auth()->id(),
                    ]);
                    $imported++;
                }
            }

            DB::commit();
            Cache::forget('settings');

            return redirect()
                ->route('system.settings.index')
                ->with('success', "Successfully imported {$imported} settings");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to default
     */
    public function reset(?string $group = null)
    {
        try {
            DB::beginTransaction();

            $query = Setting::where('is_editable', true);
            
            if ($group) {
                $query->where('group', $group);
            }

            $count = $query->update([
                'value' => DB::raw('default_value'),
                'updated_by' => auth()->id(),
            ]);

            DB::commit();
            Cache::forget('settings');

            $message = $group 
                ? "Reset {$count} settings in {$group} group to default values"
                : "Reset {$count} settings to default values";

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Process setting value based on type
     */
    private function processSettingValue(Request $request, Setting $setting): ?string
    {
        $key = $setting->key;

        // Handle file uploads separately (not in bulk update)
        if (in_array($setting->type, ['file', 'image'])) {
            return null;
        }

        // Handle boolean
        if ($setting->type === 'boolean') {
            return $request->has($key) ? 'true' : 'false';
        }

        // Handle password - only update if not empty
        if ($setting->type === 'password') {
            $value = $request->input($key);
            return !empty($value) ? $value : null;
        }

        // Handle other types
        if ($request->has($key)) {
            $value = $request->input($key);
            
            // Validate based on type
            switch ($setting->type) {
                case 'email':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Invalid email format for {$key}");
                    }
                    break;
                    
                case 'url':
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        throw new \Exception("Invalid URL format for {$key}");
                    }
                    break;
                    
                case 'integer':
                    if (!empty($value) && !is_numeric($value)) {
                        throw new \Exception("Invalid number format for {$key}");
                    }
                    break;
            }
            
            return $value;
        }

        return null;
    }
}