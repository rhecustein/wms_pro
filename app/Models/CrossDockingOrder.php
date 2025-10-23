<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrossDockingOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cross_dock_number',
        'warehouse_id',
        'inbound_shipment_id',
        'outbound_order_id',
        'product_id',
        'quantity',
        'unit_of_measure',
        'status',
        'scheduled_date',
        'started_at',
        'completed_at',
        'dock_in',
        'dock_out',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inboundShipment()
    {
        return $this->belongsTo(InboundShipment::class);
    }

    public function outboundOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'outbound_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}