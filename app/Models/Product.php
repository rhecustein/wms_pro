<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'description',
        'category_id',
        'unit_of_measure',
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'packaging_type',
        'is_batch_tracked',
        'is_serial_tracked',
        'is_expiry_tracked',
        'shelf_life_days',
        'reorder_level',
        'reorder_quantity',
        'min_stock_level',
        'max_stock_level',
        'is_hazmat',
        'temperature_min',
        'temperature_max',
        'is_active',
        'image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_batch_tracked' => 'boolean',
        'is_serial_tracked' => 'boolean',
        'is_expiry_tracked' => 'boolean',
        'is_hazmat' => 'boolean',
        'is_active' => 'boolean',
        'weight_kg' => 'decimal:2',
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }
}