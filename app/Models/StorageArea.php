<?php
// app/Models/StorageArea.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageArea extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'code',
        'name',
        'type',
        'area_sqm',
        'height_meters',
        'capacity_pallets',
        'is_active',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'area_sqm' => 'decimal:2',
        'height_meters' => 'decimal:2'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function storageBins()
    {
        return $this->hasMany(StorageBin::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getTypeNameAttribute()
    {
        $types = [
            'spr' => 'SPR (Standard Pallet Rack)',
            'bulky' => 'Bulky Storage',
            'quarantine' => 'Quarantine Area',
            'staging_1' => 'Staging Area 1',
            'staging_2' => 'Staging Area 2',
            'virtual' => 'Virtual Storage'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeBadgeColorAttribute()
    {
        $colors = [
            'spr' => 'blue',
            'bulky' => 'purple',
            'quarantine' => 'red',
            'staging_1' => 'yellow',
            'staging_2' => 'orange',
            'virtual' => 'gray'
        ];

        return $colors[$this->type] ?? 'gray';
    }
}