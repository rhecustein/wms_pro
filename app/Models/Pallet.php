<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pallet_number',
        'pallet_type',
        'barcode',
        'qr_code',
        'width_cm',
        'depth_cm',
        'height_cm',
        'max_weight_kg',
        'current_weight_kg',
        'storage_bin_id',
        'status',
        'is_available',
        'last_used_date',
        'condition',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'width_cm' => 'decimal:2',
        'depth_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'max_weight_kg' => 'decimal:2',
        'current_weight_kg' => 'decimal:2',
        'is_available' => 'boolean',
        'last_used_date' => 'datetime',  // Ini yang penting untuk fix error
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the storage bin that the pallet is located in.
     */
    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    /**
     * Get the user who created the pallet.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the pallet.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Calculate the volume of the pallet in cubic meters.
     */
    public function getVolumeAttribute()
    {
        return ($this->width_cm * $this->depth_cm * $this->height_cm) / 1000000;
    }

    /**
     * Calculate capacity utilization percentage.
     */
    public function getCapacityUtilizationAttribute()
    {
        if ($this->max_weight_kg == 0) {
            return 0;
        }
        return ($this->current_weight_kg / $this->max_weight_kg) * 100;
    }

    /**
     * Scope a query to only include available pallets.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->where('status', '!=', 'damaged');
    }

    /**
     * Scope a query to only include pallets by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pallets by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('pallet_type', $type);
    }

    /**
     * Scope a query to only include pallets by condition.
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Get the warehouse through storage bin relationship.
     */
    public function getWarehouseAttribute()
    {
        return $this->storageBin?->warehouse;
    }
}