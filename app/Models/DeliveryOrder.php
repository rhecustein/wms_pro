<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'do_number',
        'sales_order_id',
        'packing_order_id',
        'warehouse_id',
        'customer_id',
        'delivery_date',
        'vehicle_id',
        'driver_id',
        'status',
        'total_boxes',
        'total_weight_kg',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'loaded_at',
        'departed_at',
        'delivered_at',
        'received_by_name',
        'received_by_signature',
        'delivery_proof_image',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_weight_kg' => 'decimal:2',
        'loaded_at' => 'datetime',
        'departed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function packingOrder()
    {
        return $this->belongsTo(PackingOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }
}