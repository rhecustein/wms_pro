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
        'quantity_requested' => 'decimal:2',
        'quantity_picked' => 'decimal:2',
        'picked_at' => 'datetime',
    ];

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

    public function pickedByUser()
    {
        return $this->belongsTo(User::class, 'picked_by');
    }
}