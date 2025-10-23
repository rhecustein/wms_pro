<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'so_number',
        'warehouse_id',
        'customer_id',
        'order_date',
        'requested_delivery_date',
        'status',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'requested_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function pickingOrders()
    {
        return $this->hasMany(PickingOrder::class);
    }
}