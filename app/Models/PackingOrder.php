<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'packing_number',
        'picking_order_id',
        'sales_order_id',
        'warehouse_id',
        'packing_date',
        'status',
        'assigned_to',
        'started_at',
        'completed_at',
        'total_boxes',
        'total_weight_kg',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'packing_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_weight_kg' => 'decimal:2',
    ];

    public function pickingOrder()
    {
        return $this->belongsTo(PickingOrder::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items()
    {
        return $this->hasMany(PackingOrderItem::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }
}
