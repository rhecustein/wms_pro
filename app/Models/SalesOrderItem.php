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
        'quantity_ordered' => 'decimal:2',
        'quantity_picked' => 'decimal:2',
        'quantity_packed' => 'decimal:2',
        'quantity_shipped' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}