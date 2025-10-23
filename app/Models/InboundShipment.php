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
        'shipment_date',
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

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-clock mr-1"></i>Scheduled</span>',
            'arrived' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-truck mr-1"></i>Arrived</span>',
            'unloading' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-boxes mr-1"></i>Unloading</span>',
            'received' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-check mr-1"></i>Received</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getProgressPercentageAttribute()
    {
        if (!$this->expected_pallets || $this->expected_pallets == 0) {
            return 0;
        }

        return min(100, round(($this->received_pallets / $this->expected_pallets) * 100, 1));
    }
}