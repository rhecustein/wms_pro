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
        'supplier_id',
        'scheduled_date',
        'shipment_date',
        'arrival_date',
        'unloading_start',
        'unloading_end',
        'completed_at',
        'expected_pallets',
        'received_pallets',
        'expected_boxes',
        'received_boxes',
        'expected_weight',
        'actual_weight',
        'vehicle_type',
        'vehicle_number',
        'container_number',
        'driver_name',
        'driver_phone',
        'driver_id_number',
        'seal_number',
        'status',
        'dock_number',
        'received_by',
        'inspected_by',
        'bill_of_lading',
        'packing_list',
        'attachments',
        'inspection_result',
        'inspection_notes',
        'has_damages',
        'damage_description',
        'notes',
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
        'attachments' => 'array',
        'has_damages' => 'boolean',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items()
    {
        return $this->hasMany(InboundShipmentItem::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-calendar-check mr-1"></i>Scheduled</span>',
            'in_transit' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800"><i class="fas fa-shipping-fast mr-1"></i>In Transit</span>',
            'arrived' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-truck mr-1"></i>Arrived</span>',
            'unloading' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-boxes mr-1"></i>Unloading</span>',
            'inspection' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-search mr-1"></i>Inspection</span>',
            'received' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800"><i class="fas fa-check mr-1"></i>Received</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getInspectionResultBadgeAttribute()
    {
        if (!$this->inspection_result) return '-';
        
        $badges = [
            'passed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Passed</span>',
            'failed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Failed</span>',
            'partial' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i>Partial</span>',
        ];

        return $badges[$this->inspection_result] ?? $this->inspection_result;
    }

    public function getPalletProgressAttribute()
    {
        if (!$this->expected_pallets || $this->expected_pallets == 0) return 0;
        
        return min(100, round(($this->received_pallets / $this->expected_pallets) * 100, 1));
    }

    public function getBoxProgressAttribute()
    {
        if (!$this->expected_boxes || $this->expected_boxes == 0) return 0;
        
        return min(100, round(($this->received_boxes / $this->expected_boxes) * 100, 1));
    }

    public function getUnloadingDurationAttribute()
    {
        if (!$this->unloading_start || !$this->unloading_end) return null;
        
        return $this->unloading_start->diffInMinutes($this->unloading_end);
    }

    // Generate Shipment Number
    public static function generateShipmentNumber()
    {
        $lastShipment = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $lastShipment ? intval(substr($lastShipment->shipment_number, 4)) + 1 : 1;
        return 'ISH-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('arrival_date', today());
    }
}