<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'picking_order_id',
        'sales_order_item_id',
        'product_id',
        'storage_bin_id',
        'batch_number',
        'serial_number',
        'expiry_date',
        'quantity_requested',
        'quantity_picked',
        'unit_of_measure',
        'pick_sequence',
        'status',
        'picked_by',
        'picked_at',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'picked_at' => 'datetime',
    ];

    // Relationships
    public function pickingOrder()
    {
        return $this->belongsTo(PickingOrder::class);
    }

    public function salesOrderItem()
    {
        return $this->belongsTo(SalesOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function pickedBy()
    {
        return $this->belongsTo(User::class, 'picked_by');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">Pending</span>',
            'picked' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">Picked</span>',
            'short' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">Short</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }
}