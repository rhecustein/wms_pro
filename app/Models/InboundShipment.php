<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_number',
        'purchase_order_id',
        'warehouse_id',
        'vendor_id',
        'arrival_date',
        'expected_pallets',
        'received_pallets',
        'vehicle_number',
        'driver_name',
        'driver_phone',
        'seal_number',
        'status',
        'dock_number',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
    ];

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

    public function goodReceivings()
    {
        return $this->hasMany(GoodReceiving::class);
    }
}