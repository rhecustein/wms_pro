<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'product_id',
        'quantity_ordered',
        'quantity_picked',
        'quantity_packed',
        'quantity_shipped',
        'unit_price',
        'tax_rate',
        'discount_rate',
        'line_total',
        'notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_picked' => 'integer',
        'quantity_packed' => 'integer',
        'quantity_shipped' => 'integer',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessors
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalAfterDiscountAttribute()
    {
        return $this->subtotal - $this->discount;
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - ($this->picked_quantity ?? 0);
    }

    public function getPickedPercentageAttribute()
    {
        if ($this->quantity == 0) {
            return 0;
        }
        return round((($this->picked_quantity ?? 0) / $this->quantity) * 100, 2);
    }
}