<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'return_orders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'return_number',
        'delivery_order_id',
        'sales_order_id',
        'warehouse_id',
        'customer_id',
        'return_date',
        'return_type',
        'status',
        'disposition',
        'total_items',
        'total_quantity',
        'refund_amount',
        'refund_status',
        'refund_processed_at',
        'rejection_reason',
        'notes',
        'inspected_by',
        'inspected_at',
        'received_by',
        'received_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'return_date' => 'datetime',
        'inspected_at' => 'datetime',
        'received_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
        'refund_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'status_badge',
        'return_type_badge',
        'refund_status_badge',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the warehouse that owns the return order.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the customer that owns the return order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the delivery order that owns the return order.
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    /**
     * Get the sales order that owns the return order.
     */
    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    /**
     * Get the items for the return order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReturnOrderItem::class);
    }

    /**
     * Get the user who inspected the return.
     */
    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    /**
     * Get the user who received the return.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the user who created the return.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the return.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get status badge HTML.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                <i class="fas fa-clock mr-1"></i>Pending
            </span>',
            'received' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                <i class="fas fa-inbox mr-1"></i>Received
            </span>',
            'inspected' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                <i class="fas fa-search mr-1"></i>Inspected
            </span>',
            'restocked' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Restocked
            </span>',
            'disposed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                <i class="fas fa-trash mr-1"></i>Disposed
            </span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                <i class="fas fa-times-circle mr-1"></i>Cancelled
            </span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    /**
     * Get return type badge HTML.
     */
    public function getReturnTypeBadgeAttribute(): string
    {
        $badges = [
            'customer_return' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                <i class="fas fa-undo mr-1"></i>Customer Return
            </span>',
            'damaged' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                <i class="fas fa-exclamation-triangle mr-1"></i>Damaged
            </span>',
            'expired' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                <i class="fas fa-calendar-times mr-1"></i>Expired
            </span>',
            'wrong_item' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                <i class="fas fa-exchange-alt mr-1"></i>Wrong Item
            </span>',
        ];

        return $badges[$this->return_type] ?? '';
    }

    /**
     * Get refund status badge HTML.
     */
    public function getRefundStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                <i class="fas fa-clock mr-1"></i>Pending
            </span>',
            'approved' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                <i class="fas fa-check mr-1"></i>Approved
            </span>',
            'processed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Processed
            </span>',
            'rejected' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                <i class="fas fa-times-circle mr-1"></i>Rejected
            </span>',
        ];

        return $badges[$this->refund_status] ?? $badges['pending'];
    }

    /**
     * Get formatted return number.
     */
    public function getFormattedReturnNumberAttribute(): string
    {
        return strtoupper($this->return_number);
    }

    /**
     * Get formatted return date.
     */
    public function getFormattedReturnDateAttribute(): string
    {
        return $this->return_date ? $this->return_date->format('d M Y, H:i') : '-';
    }

    /**
     * Get human readable return type.
     */
    public function getReturnTypeNameAttribute(): string
    {
        $types = [
            'customer_return' => 'Customer Return',
            'damaged' => 'Damaged',
            'expired' => 'Expired',
            'wrong_item' => 'Wrong Item',
        ];

        return $types[$this->return_type] ?? ucfirst($this->return_type);
    }

    /**
     * Get human readable status.
     */
    public function getStatusNameAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Check if return can be edited.
     */
    public function getCanEditAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if return can be deleted.
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if return can be received.
     */
    public function getCanReceiveAttribute(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if return can be inspected.
     */
    public function getCanInspectAttribute(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if return can be restocked.
     */
    public function getCanRestockAttribute(): bool
    {
        return $this->status === 'inspected';
    }

    /**
     * Check if return can be cancelled.
     */
    public function getCanCancelAttribute(): bool
    {
        return in_array($this->status, ['pending', 'received']);
    }

    // ==================== QUERY SCOPES ====================

    /**
     * Scope a query to only include returns with a specific status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include returns of a specific type.
     */
    public function scopeReturnType($query, string $type)
    {
        return $query->where('return_type', $type);
    }

    /**
     * Scope a query to only include returns for a specific warehouse.
     */
    public function scopeWarehouse($query, int $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Scope a query to only include returns for a specific customer.
     */
    public function scopeCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope a query to only include pending returns.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include received returns.
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to only include inspected returns.
     */
    public function scopeInspected($query)
    {
        return $query->where('status', 'inspected');
    }

    /**
     * Scope a query to only include restocked returns.
     */
    public function scopeRestocked($query)
    {
        return $query->where('status', 'restocked');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('return_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search by return number or customer name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('return_number', 'like', "%{$search}%")
              ->orWhereHas('customer', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
              });
        });
    }

    // ==================== BOOT METHOD ====================

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate return number when creating
        static::creating(function ($model) {
            if (empty($model->return_number)) {
                $model->return_number = static::generateReturnNumber();
            }
        });

        // Update totals before saving
        static::saving(function ($model) {
            if ($model->isDirty(['total_items', 'total_quantity'])) {
                // Totals are being updated
            }
        });
    }

    // ==================== HELPER METHODS ====================

    /**
     * Generate unique return number.
     */
    public static function generateReturnNumber(): string
    {
        $prefix = 'RET-';
        $date = date('Ymd');
        
        $lastReturn = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastReturn 
            ? intval(substr($lastReturn->return_number, -4)) + 1 
            : 1;

        return $prefix . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate and update totals from items.
     */
    public function calculateTotals(): void
    {
        $this->total_items = $this->items()->count();
        $this->total_quantity = $this->items()->sum('quantity_returned');
        $this->refund_amount = $this->items()->sum('refund_amount');
        $this->save();
    }

    /**
     * Mark return as received.
     */
    public function markAsReceived(int $userId): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'received',
            'received_by' => $userId,
            'received_at' => now(),
            'updated_by' => $userId,
        ]);
    }

    /**
     * Mark return as inspected.
     */
    public function markAsInspected(int $userId, string $disposition): bool
    {
        if ($this->status !== 'received') {
            return false;
        }

        return $this->update([
            'status' => 'inspected',
            'disposition' => $disposition,
            'inspected_by' => $userId,
            'inspected_at' => now(),
            'updated_by' => $userId,
        ]);
    }

    /**
     * Mark return as restocked.
     */
    public function markAsRestocked(int $userId): bool
    {
        if ($this->status !== 'inspected') {
            return false;
        }

        return $this->update([
            'status' => 'restocked',
            'updated_by' => $userId,
        ]);
    }

    /**
     * Cancel return order.
     */
    public function cancel(int $userId): bool
    {
        if (!in_array($this->status, ['pending', 'received'])) {
            return false;
        }

        return $this->update([
            'status' => 'cancelled',
            'updated_by' => $userId,
        ]);
    }
}