<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'status',
        'warehouse_id',
        'storage_bin_id',
        'max_weight_kg',
        'current_weight_kg',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'max_weight_kg' => 'decimal:2',
        'current_weight_kg' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // RELASI
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }
}