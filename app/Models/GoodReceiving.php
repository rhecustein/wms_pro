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

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-file mr-1"></i>Draft</span>',
            'in_progress' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-spinner mr-1"></i>In Progress</span>',
            'quality_check' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-check-circle mr-1"></i>Quality Check</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-double mr-1"></i>Completed</span>',
            'partial' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-exclamation-triangle mr-1"></i>Partial</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getQualityStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'passed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Passed</span>',
            'failed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Failed</span>',
            'partial' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-minus-circle mr-1"></i>Partial</span>',
        ];

        return $badges[$this->quality_status] ?? $this->quality_status;
    }
}