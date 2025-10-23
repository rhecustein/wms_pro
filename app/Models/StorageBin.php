<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageBin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'is_occupied',
        'storage_area_id',
        'bin_code',
        'aisle',
        'row',
        'column',
        'level',
        'bin_type',
        'max_weight_kg',
        'max_pallets',
        'width_cm',
        'depth_cm',
        'height_cm',
        'min_quantity',
        'max_quantity',
        'is_active',
        'is_occupied',
        'current_stock_qty',
        'packaging_type_restriction',
        'customer_restriction_id',
        'temperature_controlled',
        'hazmat_approved',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_occupied' => 'boolean',
        'temperature_controlled' => 'boolean',
        'hazmat_approved' => 'boolean',
        'max_weight_kg' => 'decimal:2',
        'current_stock_qty' => 'decimal:2',
    ];

    public function storageArea()
    {
        return $this->belongsTo(StorageArea::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function pallets()
    {
        return $this->hasMany(Pallet::class);
    }

    //customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function customerRestriction()
    {
        return $this->belongsTo(Customer::class, 'customer_restriction_id');
    }
}