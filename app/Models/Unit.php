<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_code',
        'type',
        'base_unit_conversion',
        'base_unit_id',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_unit_conversion' => 'decimal:4',
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

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function derivedUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Active
            </span>';
        }
        return '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
            <i class="fas fa-times-circle mr-1"></i>Inactive
        </span>';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBaseUnits($query)
    {
        return $query->whereNull('base_unit_id');
    }
}