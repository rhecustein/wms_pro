<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_order_id',
        'product_id',
        'from_storage_bin_id',
        'to_storage_bin_id',
        'batch_number',
        'serial_number',
        'quantity_requested',
        'quantity_shipped',
        'quantity_received',
        'unit_of_measure',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_shipped' => 'decimal:2',
        'quantity_received' => 'decimal:2',
    ];

    public function transferOrder()
    {
        return $this->belongsTo(TransferOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromStorageBin()
    {
        return $this->belongsTo(StorageBin::class, 'from_storage_bin_id');
    }

    public function toStorageBin()
    {
        return $this->belongsTo(StorageBin::class, 'to_storage_bin_id');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>',
            'shipped' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Shipped</span>',
            'received' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Received</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }
}