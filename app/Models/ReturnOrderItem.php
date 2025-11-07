<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnOrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'return_order_items';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'return_order_id',
        'product_id',
        'batch_number',
        'serial_number',
        'quantity_returned',
        'quantity_restocked',
        'quantity_disposed',
        'condition',
        'disposition',
        'restocked_to_bin_id',
        'quarantine_bin_id',
        'return_reason',
        'inspection_notes',
        'notes',
        'unit_price',
        'refund_amount',
        'inspected_at',
        'restocked_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity_returned' => 'integer',
        'quantity_restocked' => 'integer',
        'quantity_disposed' => 'integer',
        'unit_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'inspected_at' => 'datetime',
        'restocked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'condition_badge',
        'disposition_badge',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the return order that owns the item.
     */
    public function returnOrder(): BelongsTo
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the storage bin where item was restocked.
     */
    public function restockedToBin(): BelongsTo
    {
        return $this->belongsTo(StorageBin::class, 'restocked_to_bin_id');
    }

    /**
     * Get the quarantine bin if item is quarantined.
     */
    public function quarantineBin(): BelongsTo
    {
        return $this->belongsTo(StorageBin::class, 'quarantine_bin_id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get condition badge HTML.
     */
    public function getConditionBadgeAttribute(): string
    {
        $badges = [
            'good' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Good
            </span>',
            'damaged' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>Damaged
            </span>',
            'expired' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                <i class="fas fa-calendar-times mr-1"></i>Expired
            </span>',
            'defective' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                <i class="fas fa-tools mr-1"></i>Defective
            </span>',
        ];

        return $badges[$this->condition] ?? $badges['good'];
    }

    /**
     * Get disposition badge HTML.
     */
    public function getDispositionBadgeAttribute(): string
    {
        if (!$this->disposition) {
            return '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                <i class="fas fa-clock mr-1"></i>Pending
            </span>';
        }

        $badges = [
            'restock' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                <i class="fas fa-layer-group mr-1"></i>Restock
            </span>',
            'quarantine' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                <i class="fas fa-shield-alt mr-1"></i>Quarantine
            </span>',
            'dispose' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                <i class="fas fa-trash mr-1"></i>Dispose
            </span>',
            'rework' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                <i class="fas fa-tools mr-1"></i>Rework
            </span>',
            'return_to_supplier' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                <i class="fas fa-undo mr-1"></i>Return to Supplier
            </span>',
        ];

        return $badges[$this->disposition] ?? '';
    }

    /**
     * Get human readable condition.
     */
    public function getConditionNameAttribute(): string
    {
        $names = [
            'good' => 'Good',
            'damaged' => 'Damaged',
            'expired' => 'Expired',
            'defective' => 'Defective',
        ];

        return $names[$this->condition] ?? ucfirst($this->condition);
    }

    /**
     * Get human readable disposition.
     */
    public function getDispositionNameAttribute(): string
    {
        if (!$this->disposition) {
            return 'Pending';
        }

        $names = [
            'restock' => 'Restock',
            'quarantine' => 'Quarantine',
            'dispose' => 'Dispose',
            'rework' => 'Rework',
            'return_to_supplier' => 'Return to Supplier',
        ];

        return $names[$this->disposition] ?? ucfirst($this->disposition);
    }

    /**
     * Get quantity pending (not restocked or disposed).
     */
    public function getQuantityPendingAttribute(): int
    {
        return $this->quantity_returned - $this->quantity_restocked - $this->quantity_disposed;
    }

    /**
     * Check if item is fully processed.
     */
    public function getIsFullyProcessedAttribute(): bool
    {
        return ($this->quantity_restocked + $this->quantity_disposed) >= $this->quantity_returned;
    }

    /**
     * Get total value of returned items.
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity_returned * $this->unit_price;
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope a query to only include items with a specific condition.
     */
    public function scopeCondition($query, string $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Scope a query to only include items with a specific disposition.
     */
    public function scopeDisposition($query, string $disposition)
    {
        return $query->where('disposition', $disposition);
    }

    /**
     * Scope a query to only include good condition items.
     */
    public function scopeGoodCondition($query)
    {
        return $query->where('condition', 'good');
    }

    /**
     * Scope a query to only include damaged items.
     */
    public function scopeDamaged($query)
    {
        return $query->where('condition', 'damaged');
    }

    /**
     * Scope a query to only include expired items.
     */
    public function scopeExpired($query)
    {
        return $query->where('condition', 'expired');
    }

    /**
     * Scope a query to only include restocked items.
     */
    public function scopeRestocked($query)
    {
        return $query->where('quantity_restocked', '>', 0);
    }

    /**
     * Scope a query to only include pending items (not fully processed).
     */
    public function scopePending($query)
    {
        return $query->whereRaw('(quantity_restocked + quantity_disposed) < quantity_returned');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Mark item as inspected.
     */
    public function markAsInspected(string $disposition, ?string $notes = null): bool
    {
        return $this->update([
            'disposition' => $disposition,
            'inspection_notes' => $notes,
            'inspected_at' => now(),
        ]);
    }

    /**
     * Restock item to a bin.
     */
    public function restockTo(int $binId, int $quantity): bool
    {
        if ($quantity > $this->quantity_pending) {
            return false;
        }

        return $this->update([
            'quantity_restocked' => $this->quantity_restocked + $quantity,
            'restocked_to_bin_id' => $binId,
            'restocked_at' => now(),
        ]);
    }

    /**
     * Move item to quarantine.
     */
    public function moveToQuarantine(int $binId): bool
    {
        return $this->update([
            'disposition' => 'quarantine',
            'quarantine_bin_id' => $binId,
        ]);
    }

    /**
     * Dispose item.
     */
    public function dispose(int $quantity): bool
    {
        if ($quantity > $this->quantity_pending) {
            return false;
        }

        return $this->update([
            'quantity_disposed' => $this->quantity_disposed + $quantity,
            'disposition' => 'dispose',
        ]);
    }

    /**
     * Calculate refund amount based on unit price and quantity.
     */
    public function calculateRefund(float $refundPercentage = 100): void
    {
        $this->refund_amount = ($this->unit_price * $this->quantity_returned) * ($refundPercentage / 100);
        $this->save();
    }

    // ==================== BOOT METHOD ====================

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Calculate refund amount if unit price is set
        static::creating(function ($model) {
            if ($model->unit_price > 0 && $model->refund_amount == 0) {
                $model->refund_amount = $model->unit_price * $model->quantity_returned;
            }
        });

        // Update return order totals when item is saved
        static::saved(function ($model) {
            if ($model->returnOrder) {
                $model->returnOrder->calculateTotals();
            }
        });

        // Update return order totals when item is deleted
        static::deleted(function ($model) {
            if ($model->returnOrder) {
                $model->returnOrder->calculateTotals();
            }
        });
    }
}