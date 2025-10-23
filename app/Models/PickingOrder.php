<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickingOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'picking_number',
        'sales_order_id',
        'warehouse_id',
        'picking_date',
        'picking_type',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'started_at',
        'completed_at',
        'total_items',
        'total_quantity',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'picking_date' => 'date',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_quantity' => 'decimal:2',
    ];

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
        return $this->hasMany(PickingOrderItem::class);
    }
}