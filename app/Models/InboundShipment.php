<?php
// app/Models/InboundShipment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InboundShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shipment_number',
        'purchase_order_id',
        'warehouse_id',
        'supplier_id', // CHANGED from vendor_id
        
        // Dates & Times
        'scheduled_date',
        'shipment_date',
        'arrival_date',
        'unloading_start',
        'unloading_end',
        'completed_at',
        
        // Shipment Details
        'expected_pallets',
        'received_pallets',
        'expected_boxes',
        'received_boxes',
        'expected_weight',
        'actual_weight',
        
        // Vehicle & Driver Info
        'vehicle_type',
        'vehicle_number',
        'container_number',
        'driver_name',
        'driver_phone',
        'driver_id_number',
        'seal_number',
        
        // Warehouse Operations
        'status',
        'dock_number',
        'received_by',
        'inspected_by',
        
        // Shipping Documents
        'bill_of_lading',
        'packing_list',
        'attachments',
        
        // Quality Check
        'inspection_result',
        'inspection_notes',
        'has_damages',
        'damage_description',
        
        'notes',
        
        // Audit
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'shipment_date' => 'datetime',
        'arrival_date' => 'datetime',
        'unloading_start' => 'datetime',
        'unloading_end' => 'datetime',
        'completed_at' => 'datetime',
        'expected_pallets' => 'integer',
        'received_pallets' => 'integer',
        'expected_boxes' => 'integer',
        'received_boxes' => 'integer',
        'expected_weight' => 'decimal:2',
        'actual_weight' => 'decimal:2',
        'has_damages' => 'boolean',
        'attachments' => 'array',
    ];

    // Relationships
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InboundShipmentItem::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier(): BelongsTo // CHANGED from vendor()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    // Accessors & Mutators
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'scheduled' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-calendar-check mr-1.5"></i>Scheduled</span>',
            'in_transit' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-shipping-fast mr-1.5"></i>In Transit</span>',
            'arrived' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-box-open mr-1.5"></i>Arrived</span>',
            'unloading' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-dolly mr-1.5"></i>Unloading</span>',
            'inspection' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-indigo-100 to-indigo-200 text-indigo-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-clipboard-check mr-1.5"></i>Inspection</span>',
            'received' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-teal-100 to-teal-200 text-teal-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-check-double mr-1.5"></i>Received</span>',
            'completed' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-check-circle mr-1.5"></i>Completed</span>',
            'cancelled' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-red-100 to-red-200 text-red-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-times-circle mr-1.5"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Unknown</span>';
    }

    public function getInspectionResultBadgeAttribute(): string
    {
        if (!$this->inspection_result) {
            return '<span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold"><i class="fas fa-question-circle mr-1.5"></i>Not Inspected</span>';
        }

        $badges = [
            'passed' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-check-circle mr-1.5"></i>Passed</span>',
            'failed' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-red-100 to-red-200 text-red-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-times-circle mr-1.5"></i>Failed</span>',
            'partial' => '<span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 rounded-full text-xs font-semibold shadow-sm"><i class="fas fa-exclamation-circle mr-1.5"></i>Partial</span>',
        ];

        return $badges[$this->inspection_result] ?? '<span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">Unknown</span>';
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->expected_pallets || $this->expected_pallets <= 0) {
            return 0;
        }

        return round(($this->received_pallets / $this->expected_pallets) * 100, 2);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getCanEditAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'in_transit', 'arrived']);
    }

    public function getCanDeleteAttribute(): bool
    {
        return in_array($this->status, ['scheduled', 'cancelled']);
    }

    public function getCanCancelAttribute(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeArrived($query)
    {
        return $query->where('status', 'arrived');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeForWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeForSupplier($query, $supplierId) // CHANGED from ForVendor
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('arrival_date', [$startDate, $endDate]);
    }

    // Helper Methods
    public static function generateShipmentNumber(): string
    {
        $lastShipment = static::latest('id')->first();
        $nextNumber = $lastShipment ? ((int) substr($lastShipment->shipment_number, 4)) + 1 : 1;
        return 'ISH-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function calculateUnloadingDuration(): ?int
    {
        if ($this->unloading_start && $this->unloading_end) {
            return $this->unloading_start->diffInMinutes($this->unloading_end);
        }
        return null;
    }

    public function isDelayed(): bool
    {
        if (!$this->arrival_date || $this->status === 'completed') {
            return false;
        }

        return now()->gt($this->arrival_date) && !in_array($this->status, ['arrived', 'unloading', 'inspection', 'received', 'completed']);
    }

    public function getDaysDelayed(): ?int
    {
        if (!$this->isDelayed()) {
            return null;
        }

        return now()->diffInDays($this->arrival_date);
    }

    public function getFormattedScheduledDateAttribute(): ?string
    {
        return $this->scheduled_date ? $this->scheduled_date->format('d M Y H:i') : null;
    }

    public function getFormattedShipmentDateAttribute(): ?string
    {
        return $this->shipment_date ? $this->shipment_date->format('d M Y H:i') : null;
    }

    public function getFormattedArrivalDateAttribute(): ?string
    {
        return $this->arrival_date ? $this->arrival_date->format('d M Y H:i') : null;
    }

    public function getFormattedCompletedAtAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->format('d M Y H:i') : null;
    }

    public function getUnloadingDurationFormattedAttribute(): ?string
    {
        $duration = $this->calculateUnloadingDuration();
        
        if (!$duration) {
            return null;
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'blue',
            'in_transit' => 'purple',
            'arrived' => 'yellow',
            'unloading' => 'orange',
            'inspection' => 'indigo',
            'received' => 'teal',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getVariancePercentageAttribute(): ?float
    {
        if (!$this->expected_pallets || $this->expected_pallets <= 0) {
            return null;
        }

        $variance = $this->received_pallets - $this->expected_pallets;
        return round(($variance / $this->expected_pallets) * 100, 2);
    }

    public function hasDiscrepancy(): bool
    {
        return $this->expected_pallets > 0 && $this->received_pallets !== $this->expected_pallets;
    }

    public function getDiscrepancyTypeAttribute(): ?string
    {
        if (!$this->hasDiscrepancy()) {
            return null;
        }

        return $this->received_pallets > $this->expected_pallets ? 'overage' : 'shortage';
    }
}