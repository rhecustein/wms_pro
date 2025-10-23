<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_number',
        'delivery_order_id',
        'sales_order_id',
        'warehouse_id',
        'customer_id',
        'return_date',
        'return_type',
        'status',
        'total_items',
        'total_quantity',
        'inspected_by',
        'inspected_at',
        'disposition',
        'refund_amount',
        'refund_status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_quantity' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'inspected_at' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function inspectedByUser()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnOrderItem::class);
    }
}