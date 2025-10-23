<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_order_id',
        'picking_order_item_id',
        'product_id',
        'batch_number',
        'serial_number',
        'quantity_packed',
        'box_number',
        'box_weight_kg',
        'packed_by',
        'packed_at',
    ];

    protected $casts = [
        'packed_at' => 'datetime',
        'box_weight_kg' => 'decimal:2',
    ];

    // Relationships
    public function packingOrder()
    {
        return $this->belongsTo(PackingOrder::class);
    }

    public function pickingOrderItem()
    {
        return $this->belongsTo(PickingOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function packedBy()
    {
        return $this->belongsTo(User::class, 'packed_by');
    }
}