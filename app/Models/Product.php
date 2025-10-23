<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'unit_of_measure',
        'barcode',
        'sku',
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'packaging_type',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'cost_price',
        'selling_price',
        'has_serial_number',
        'has_batch_number',
        'has_expiry_date',
        'shelf_life_days',
        'is_hazmat',
        'is_fragile',
        'current_stock',
        'cost_price',
        'selling_price',
        'stock_value',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'min_stock_level' => 'decimal:2',
        'max_stock_level' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'has_serial_number' => 'boolean',
        'has_batch_number' => 'boolean',
        'has_expiry_date' => 'boolean',
        'shelf_life_days' => 'integer',
        'is_hazmat' => 'boolean',
        'is_fragile' => 'boolean',
        'is_active' => 'boolean',
    ];

    // RELASI

    //stockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function pickingOrderItems()
    {
        return $this->hasMany(PickingOrderItem::class);
    }

    public function packingOrderItems()
    {
        return $this->hasMany(PackingOrderItem::class);
    }

    public function returnOrderItems()
    {
        return $this->hasMany(ReturnOrderItem::class);
    }

    public function transferOrderItems()
    {
        return $this->hasMany(TransferOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}