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
        'unit_of_measure',
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
    public function getQuantityAttribute()
    {
        return $this->quantity_ordered;
    }

    public function getSubtotalAttribute()
    {
        return $this->quantity_ordered * $this->unit_price;
    }

    public function getDiscountAttribute()
    {
        return $this->subtotal * ($this->discount_rate / 100);
    }

    public function getTotalAfterDiscountAttribute()
    {
        return $this->subtotal - $this->discount;
    }

    public function getTaxAttribute()
    {
        return $this->total_after_discount * ($this->tax_rate / 100);
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity_ordered - $this->quantity_picked;
    }

    public function getPickedQuantityAttribute()
    {
        return $this->quantity_picked;
    }

    public function getPickedPercentageAttribute()
    {
        if ($this->quantity_ordered == 0) {
            return 0;
        }
        return round(($this->quantity_picked / $this->quantity_ordered) * 100, 2);
    }

    public function getIsFullyPickedAttribute()
    {
        return $this->quantity_picked >= $this->quantity_ordered;
    }

    public function getIsPartiallyPickedAttribute()
    {
        return $this->quantity_picked > 0 && $this->quantity_picked < $this->quantity_ordered;
    }

    public function getUnitOfMeasureAttribute($value)
    {
        return $value ?? 'PCS';
    }

    /**
     * Scopes
     */
    public function scopeFullyPicked($query)
    {
        return $query->whereColumn('quantity_picked', '>=', 'quantity_ordered');
    }

    public function scopePartiallyPicked($query)
    {
        return $query->where('quantity_picked', '>', 0)
                    ->whereColumn('quantity_picked', '<', 'quantity_ordered');
    }

    public function scopeNotPicked($query)
    {
        return $query->where('quantity_picked', 0);
    }
}