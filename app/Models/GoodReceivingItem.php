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
        'quantity_expected' => 'integer',
        'quantity_received' => 'integer',
        'quantity_accepted' => 'integer',
        'quantity_rejected' => 'integer',
    ];

    /**
     * Relationships
     */
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

    /**
     * Accessors
     */
    public function getQuantityDamagedAttribute()
    {
        return $this->quantity_received - $this->quantity_accepted - $this->quantity_rejected;
    }

    public function getVarianceAttribute()
    {
        return $this->quantity_received - $this->quantity_expected;
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->quantity_expected == 0) {
            return 0;
        }
        return round(($this->variance / $this->quantity_expected) * 100, 2);
    }

    public function getIsCompleteAttribute()
    {
        return $this->quantity_received >= $this->quantity_expected;
    }

    public function getIsPartialAttribute()
    {
        return $this->quantity_received > 0 && $this->quantity_received < $this->quantity_expected;
    }

    public function getHasRejectionAttribute()
    {
        return $this->quantity_rejected > 0;
    }

    public function getAcceptanceRateAttribute()
    {
        if ($this->quantity_received == 0) {
            return 0;
        }
        return round(($this->quantity_accepted / $this->quantity_received) * 100, 2);
    }

    public function getQualityStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'passed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Passed</span>',
            'failed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Failed</span>',
        ];

        return $badges[$this->quality_status] ?? $this->quality_status;
    }

    /**
     * Scopes
     */
    public function scopeQualityPassed($query)
    {
        return $query->where('quality_status', 'passed');
    }

    public function scopeQualityFailed($query)
    {
        return $query->where('quality_status', 'failed');
    }

    public function scopeQualityPending($query)
    {
        return $query->where('quality_status', 'pending');
    }

    public function scopeWithRejections($query)
    {
        return $query->where('quantity_rejected', '>', 0);
    }

    /**
     * Methods
     */
    public function isPassed()
    {
        return $this->quality_status === 'passed';
    }

    public function isFailed()
    {
        return $this->quality_status === 'failed';
    }

    public function isPending()
    {
        return $this->quality_status === 'pending';
    }

    public function markAsPassed()
    {
        $this->update([
            'quality_status' => 'passed',
            'quantity_accepted' => $this->quantity_received,
            'quantity_rejected' => 0,
        ]);
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'quality_status' => 'failed',
            'quantity_rejected' => $this->quantity_received,
            'quantity_accepted' => 0,
            'rejection_reason' => $reason,
        ]);
    }

    public function markAsPartial($acceptedQty, $rejectedQty, $reason = null)
    {
        $this->update([
            'quality_status' => $rejectedQty > 0 ? 'failed' : 'passed',
            'quantity_accepted' => $acceptedQty,
            'quantity_rejected' => $rejectedQty,
            'rejection_reason' => $reason,
        ]);
    }
}