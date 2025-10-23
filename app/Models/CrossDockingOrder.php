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

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'scheduled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-clock mr-1"></i>Scheduled</span>',
            'receiving' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-truck-loading mr-1"></i>Receiving</span>',
            'sorting' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-sort mr-1"></i>Sorting</span>',
            'loading' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-boxes mr-1"></i>Loading</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['scheduled'];
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInMinutes($this->completed_at);
        }
        
        if ($this->started_at) {
            return $this->started_at->diffInMinutes(now());
        }

        return null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'receiving', 'sorting', 'loading']);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper Methods
    public function canStartReceiving()
    {
        return $this->status === 'scheduled';
    }

    public function canStartSorting()
    {
        return $this->status === 'receiving';
    }

    public function canStartLoading()
    {
        return $this->status === 'sorting';
    }

    public function canComplete()
    {
        return $this->status === 'loading';
    }

    public function canCancel()
    {
        return in_array($this->status, ['scheduled', 'receiving', 'sorting', 'loading']);
    }
}