<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $settings = Setting::getGrouped();
        $groupLabels = Setting::getGroupLabels();
        $groupIcons = Setting::getGroupIcons();

        return view('system.settings.index', compact('settings', 'groupLabels', 'groupIcons'));
    }

    /**
     * Display specific group settings
     */
    public function show($group)
    {
        $settings = Setting::getByGroup($group);
        $groupLabels = Setting::getGroupLabels();
        $groupIcons = Setting::getGroupIcons();

        return view('system.settings.show', compact('settings', 'group', 'groupLabels', 'groupIcons'));
    }

    /**
     * Update specific group settings
     */
    public function updateGroup(Request $request, $group)
    {
        DB::beginTransaction();
        
        try {
            $allInputs = $request->except(['_token', '_method']);
            $updatedCount = 0;
            $errors = [];

            Log::info("=== Settings Update Started ===", [
                'group' => $group,
                'user_id' => auth()->id(),
                'inputs' => array_keys($allInputs),
            ]);

            // Get all settings for this group
            $groupSettings = Setting::where('group', $group)->get()->keyBy('key');

            foreach ($groupSettings as $key => $setting) {
                Log::info("Processing setting: {$key}", [
                    'type' => $setting->type,
                    'is_editable' => $setting->is_editable,
                    'current_value' => $setting->value,
                ]);

                if (!$setting->is_editable) {
                    Log::warning("Setting {$key} is not editable, skipping");
                    continue;
                }

                $newValue = null;

                // Handle file uploads
                if ($request->hasFile($key)) {
                    try {
                        $file = $request->file($key);
                        
                        Log::info("File upload detected for: {$key}", [
                            'original_name' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ]);
                        
                        // Validate file
                        if ($setting->type === 'image') {
                            $request->validate([
                                $key => 'required|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048'
                            ]);
                        } else {
                            $request->validate([
                                $key => 'required|file|max:5120'
                            ]);
                        }

                        // Delete old file if exists
                        if ($setting->value) {
                            $oldPath = $setting->value;
                            if (Storage::disk('public')->exists($oldPath)) {
                                Log::info("Deleting old file: {$oldPath}");
                                Storage::disk('public')->delete($oldPath);
                            }
                        }

                        // Store new file
                        $extension = $file->getClientOriginalExtension();
                        $filename = str_replace(['_', ' '], '-', $key) . '_' . time() . '.' . $extension;
                        $path = $file->storeAs('settings/' . $group, $filename, 'public');
                        
                        if (!$path) {
                            throw new \Exception("Failed to store file for {$key}");
                        }
                        
                        Log::info("File stored successfully", [
                            'key' => $key,
                            'path' => $path,
                            'full_path' => storage_path('app/public/' . $path),
                            'exists' => Storage::disk('public')->exists($path),
                        ]);
                        
                        $newValue = $path;
                        
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        Log::error("Validation failed for {$key}", [
                            'errors' => $e->errors()
                        ]);
                        throw $e;
                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$key}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $errors[] = "Failed to upload {$key}: " . $e->getMessage();
                        continue;
                    }
                }
                // Handle boolean values
                elseif ($setting->type === 'boolean') {
                    $newValue = $request->has($key) ? 'true' : 'false';
                    Log::info("Boolean value for {$key}: {$newValue}");
                }
                // Handle other input types
                elseif (isset($allInputs[$key])) {
                    $newValue = $allInputs[$key];
                    
                    // Handle JSON values
                    if ($setting->type === 'json' && is_array($newValue)) {
                        $newValue = json_encode($newValue);
                    }
                    
                    Log::info("Input value for {$key}: {$newValue}");
                }

                // Skip if no value provided (except for boolean which always has value)
                if ($newValue === null && $setting->type !== 'boolean') {
                    Log::info("No value provided for {$key}, skipping");
                    continue;
                }

                // Update setting using direct DB query to ensure it saves
                try {
                    $updateData = [
                        'value' => $newValue,
                        'updated_by' => auth()->id(),
                        'updated_at' => now(),
                    ];

                    // Use DB update for reliability
                    $affected = DB::table('settings')
                        ->where('id', $setting->id)
                        ->update($updateData);

                    if ($affected > 0) {
                        $updatedCount++;
                        Log::info("Successfully updated {$key}", [
                            'old_value' => $setting->value,
                            'new_value' => $newValue,
                            'affected_rows' => $affected,
                        ]);
                        
                        // Verify the update
                        $verifyValue = DB::table('settings')
                            ->where('id', $setting->id)
                            ->value('value');
                        
                        Log::info("Verified value in DB for {$key}: {$verifyValue}");
                        
                    } else {
                        Log::warning("No rows affected for {$key}");
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Failed to update {$key}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $errors[] = "Failed to update {$key}: " . $e->getMessage();
                }
            }

            // Clear cache
            Cache::forget('settings');
            Log::info("Settings cache cleared");
            
            DB::commit();
            Log::info("=== Settings Update Completed ===", [
                'updated_count' => $updatedCount,
                'errors_count' => count($errors),
            ]);

            if (count($errors) > 0) {
                return redirect()->back()
                    ->with('warning', "Updated {$updatedCount} setting(s) with " . count($errors) . " error(s)")
                    ->withErrors($errors);
            }

            if ($updatedCount > 0) {
                return redirect()->back()->with('success', "Successfully updated {$updatedCount} setting(s)!");
            } else {
                return redirect()->back()->with('info', 'No changes were made.');
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Settings Validation Error', [
                'errors' => $e->errors(),
                'group' => $group,
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validation failed. Please check your input.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Settings Update Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'group' => $group,
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile($key)
    {
        try {
            $setting = Setting::where('key', $key)->first();

            if (!$setting) {
                return redirect()->back()->with('error', 'Setting not found!');
            }

            if (!in_array($setting->type, ['file', 'image'])) {
                return redirect()->back()->with('error', 'This setting is not a file type!');
            }

            Log::info("Deleting file for setting: {$key}", [
                'current_value' => $setting->value,
            ]);

            // Delete file from storage
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
                Log::info("File deleted from storage: {$setting->value}");
            }

            // Update setting using DB
            DB::table('settings')
                ->where('id', $setting->id)
                ->update([
                    'value' => null,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            Log::info("Setting value cleared in database");

            // Clear cache
            Cache::forget('settings');

            return redirect()->back()->with('success', 'File deleted successfully!');
        } catch (\Exception $e) {
            Log::error('File deletion failed', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->with('error', 'Failed to delete file: ' . $e->getMessage());
        }
    }

    /**
     * Clear all cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            Log::info("All cache cleared by user: " . auth()->id());
            return redirect()->back()->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            Log::error('Cache clear failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Export settings
     */
    public function export()
    {
        try {
            $settings = Setting::all();
            $data = $settings->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'description' => $setting->description,
                ];
            });

            $filename = 'settings_export_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Settings export failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to export settings: ' . $e->getMessage());
        }
    }

    /**
     * Import settings
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (!is_array($data)) {
                return redirect()->back()->with('error', 'Invalid JSON format!');
            }

            $imported = 0;
            foreach ($data as $item) {
                $setting = Setting::where('key', $item['key'])->first();
                
                if ($setting && $setting->is_editable) {
                    DB::table('settings')
                        ->where('id', $setting->id)
                        ->update([
                            'value' => $item['value'],
                            'updated_by' => auth()->id(),
                            'updated_at' => now(),
                        ]);
                    $imported++;
                }
            }

            // Clear cache
            Cache::forget('settings');

            Log::info("Settings imported", [
                'count' => $imported,
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->with('success', "Successfully imported {$imported} settings!");
        } catch (\Exception $e) {
            Log::error('Settings import failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to import settings: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to default
     */
    public function reset($group = null)
    {
        try {
            return redirect()->back()->with('info', 'Reset feature coming soon. Please reinstall the system to reset all settings.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    /**
     * Upload file via AJAX
     */
    public function uploadFile(Request $request)
    {
        try {
            Log::info('=== File Upload via AJAX Started ===', [
                'all_inputs' => array_keys($request->all()),
                'all_files' => array_keys($request->allFiles()),
                'content_type' => $request->header('Content-Type'),
            ]);

            // Get setting key
            $key = $request->input('setting_key');
            if (!$key) {
                return response()->json(['error' => 'Setting key is required'], 400);
            }

            // Get setting
            $setting = Setting::where('key', $key)->first();
            if (!$setting) {
                return response()->json(['error' => 'Setting not found'], 404);
            }

            if (!$setting->is_editable) {
                return response()->json(['error' => 'Setting is not editable'], 403);
            }

            // Check file
            if (!$request->hasFile('file')) {
                Log::error('No file in request', [
                    'has_file' => $request->hasFile('file'),
                    'files' => $request->allFiles(),
                ]);
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $file = $request->file('file');

            Log::info('File detected', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'is_valid' => $file->isValid(),
            ]);

            // Validate file
            if ($setting->type === 'image') {
                $request->validate([
                    'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048'
                ]);
            } else {
                $request->validate([
                    'file' => 'required|file|max:5120'
                ]);
            }

            // Delete old file
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
                Log::info('Old file deleted', ['path' => $setting->value]);
            }

            // Store new file
            $extension = $file->getClientOriginalExtension();
            $filename = str_replace(['_', ' '], '-', $key) . '_' . time() . '.' . $extension;
            $directory = 'settings/' . $setting->group;
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            $path = $file->storeAs($directory, $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to store file');
            }

            // Verify file exists
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('File was not saved');
            }

            Log::info('File stored', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path),
                'exists' => Storage::disk('public')->exists($path),
            ]);

            // Update database
            DB::table('settings')
                ->where('id', $setting->id)
                ->update([
                    'value' => $path,
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);

            // Verify update
            $verifyValue = DB::table('settings')->where('id', $setting->id)->value('value');
            Log::info('Database updated', ['value' => $verifyValue]);

            // Clear cache
            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}