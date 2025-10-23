<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $casts = [
        'is_available' => 'boolean',
        'max_weight_kg' => 'decimal:2',
        'current_weight_kg' => 'decimal:2',
        'last_used_date' => 'date',
    ];

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }
}