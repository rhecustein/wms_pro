<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
        'is_editable',
        'order',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        $settings = static::getAllCached();
        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value
     */
    public static function set($key, $value, $type = 'string')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'updated_by' => auth()->id(),
            ]
        );
    }

    /**
     * Get all settings cached
     */
    public static function getAllCached()
    {
        return Cache::remember('settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get public settings only
     */
    public static function getPublic()
    {
        return static::where('is_public', true)->get();
    }

    /**
     * Get the actual value based on type
     */
    public function getActualValueAttribute()
    {
        return match($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            'file', 'image' => $this->value ? Storage::disk('public')->url($this->value) : null,
            default => $this->value,
        };
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        if (in_array($this->type, ['file', 'image']) && $this->value) {
            return Storage::disk('public')->url($this->value);
        }
        return null;
    }

    /**
     * Relationship with user who updated
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Accessor for backward compatibility
     */
    public function updater()
    {
        return $this->updatedByUser();
    }

    /**
     * Get grouped settings
     */
    public static function getGrouped()
    {
        return static::orderBy('order')
            ->get()
            ->groupBy('group');
    }

    /**
     * Get group labels
     */
    public static function getGroupLabels()
    {
        return [
            'identity' => 'Website Identity',
            'company' => 'Company Information',
            'appearance' => 'Theme & Appearance',
            'social' => 'Social Media',
            'warehouse' => 'Warehouse Settings',
            'inventory' => 'Inventory Settings',
            'notifications' => 'Notification Settings',
            'report' => 'Report Settings',
            'security' => 'Security Settings',
            'system' => 'System Settings',
            'general' => 'General Settings',
        ];
    }

    /**
     * Get group icons
     */
    public static function getGroupIcons()
    {
        return [
            'identity' => 'fa-globe',
            'company' => 'fa-building',
            'appearance' => 'fa-palette',
            'social' => 'fa-share-alt',
            'warehouse' => 'fa-warehouse',
            'inventory' => 'fa-boxes',
            'notifications' => 'fa-bell',
            'report' => 'fa-chart-bar',
            'security' => 'fa-shield-alt',
            'system' => 'fa-cogs',
            'general' => 'fa-sliders-h',
        ];
    }
}