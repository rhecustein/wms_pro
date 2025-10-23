<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodReceiving extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gr_number',
        'inbound_shipment_id',
        'purchase_order_id',
        'warehouse_id',
        'vendor_id',
        'receiving_date',
        'received_by',
        'status',
        'total_items',
        'total_quantity',
        'total_pallets',
        'quality_status',
        'quality_checked_by',
        'quality_checked_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'receiving_date' => 'date',
        'quality_checked_at' => 'datetime',
        'total_quantity' => 'decimal:2',
    ];

    public function inboundShipment()
    {
        return $this->belongsTo(InboundShipment::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(GoodReceivingItem::class);
    }

    public function putawayTasks()
    {
        return $this->hasMany(PutawayTask::class);
    }
}