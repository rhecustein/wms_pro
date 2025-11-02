<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'system_name',
        'type',
        'api_endpoint',
        'api_key',
        'client_id',
        'client_secret',
        'access_token',
        'token_expires_at',
        'status',
        'configuration',
        'last_sync_message',
        'last_synced_at',
        'sync_frequency_minutes',
        'auto_sync_enabled',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'auto_sync_enabled' => 'boolean',
        'sync_frequency_minutes' => 'integer',
        'configuration' => 'array',
    ];

    protected $hidden = [
        'api_key',
        'client_secret',
        'access_token',
    ];

    protected $appends = ['status_badge'];

    // Relations
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function inbounds(): HasMany
    {
        return $this->hasMany(Inbound::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Active
            </span>',
            'inactive' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                <i class="fas fa-pause-circle mr-1"></i>Inactive
            </span>',
            'error' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                <i class="fas fa-exclamation-circle mr-1"></i>Error
            </span>',
            'maintenance' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                <i class="fas fa-tools mr-1"></i>Maintenance
            </span>',
        ];

        return $badges[$this->status] ?? $badges['inactive'];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAutoSyncEnabled($query)
    {
        return $query->where('auto_sync_enabled', true);
    }

    // Methods
    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}