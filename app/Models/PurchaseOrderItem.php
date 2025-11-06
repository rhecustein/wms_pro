<?php
// app/Models/PurchaseOrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_sku',
        'product_name',
        'quantity_ordered',
        'quantity_received',
        'quantity_rejected',
        'unit_id',
        'unit_price',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'subtotal',
        'line_total',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'rejection_reason',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity_ordered' => 'decimal:2',
        'quantity_received' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'line_total' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    // Accessors
    public function getQuantityRemainingAttribute(): float
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    public function getReceiveProgressAttribute(): float
    {
        if ($this->quantity_ordered <= 0) {
            return 0;
        }
        return round(($this->quantity_received / $this->quantity_ordered) * 100, 2);
    }

    public function getIsFullyReceivedAttribute(): bool
    {
        return $this->quantity_received >= $this->quantity_ordered;
    }

    public function getIsPartiallyReceivedAttribute(): bool
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_ordered;
    }

    public function getHasRejectionAttribute(): bool
    {
        return $this->quantity_rejected > 0;
    }

    // Helper Methods
    public function canReceiveMore(): bool
    {
        return $this->quantity_received < $this->quantity_ordered;
    }

    public function getRemainingQuantity(): float
    {
        return $this->quantity_remaining;
    }
}