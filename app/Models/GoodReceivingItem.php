<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodReceivingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'good_receiving_id',
        'purchase_order_item_id',
        'product_id',
        'batch_number',
        'serial_number',
        'manufacturing_date',
        'expiry_date',
        'quantity_expected',
        'quantity_received',
        'quantity_accepted',
        'quantity_rejected',
        'unit_of_measure',
        'pallet_id',
        'quality_status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'quantity_expected' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_accepted' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
    ];

    public function goodReceiving()
    {
        return $this->belongsTo(GoodReceiving::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }
}