<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InboundShipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inbound_shipment_id',
        'purchase_order_item_id',
        'product_id',
        'quantity_expected',
        'quantity_received',
        'quantity_rejected',
        'quantity_accepted',
        'unit_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'serial_numbers',
        'location_id',
        'quality_status',
        'rejection_reason',
        'qc_notes',
        'notes',
    ];

    protected $casts = [
        'quantity_expected' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'serial_numbers' => 'array',
    ];

    // Relationships
    public function inboundShipment()
    {
        return $this->belongsTo(InboundShipment::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    // Accessors
    public function getQualityStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'passed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Passed</span>',
            'failed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Failed</span>',
            'quarantine' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i>Quarantine</span>',
        ];

        return $badges[$this->quality_status] ?? $badges['pending'];
    }

    public function getReceivingPercentageAttribute()
    {
        if ($this->quantity_expected == 0) return 0;
        
        return min(100, round(($this->quantity_received / $this->quantity_expected) * 100, 1));
    }

    public function getAcceptanceRateAttribute()
    {
        if ($this->quantity_received == 0) return 0;
        
        return round(($this->quantity_accepted / $this->quantity_received) * 100, 1);
    }

    public function getRejectionRateAttribute()
    {
        if ($this->quantity_received == 0) return 0;
        
        return round(($this->quantity_rejected / $this->quantity_received) * 100, 1);
    }
}