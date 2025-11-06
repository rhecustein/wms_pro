<?php
// app/Models/PurchaseOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'warehouse_id',
        'supplier_id',
        'po_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'status',
        'payment_status',
        'payment_terms',
        'payment_due_days',
        'subtotal',
        'tax_amount',
        'tax_rate',
        'discount_amount',
        'discount_rate',
        'shipping_cost',
        'other_cost',
        'total_amount',
        'paid_amount',
        'currency',
        'shipping_address',
        'shipping_method',
        'tracking_number',
        'reference_number',
        'supplier_invoice_number',
        'approved_by',
        'approved_at',
        'notes',
        'terms_conditions',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_due_days' => 'integer',
    ];

    // Relationships
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('sort_order');
    }

    public function inboundShipments(): HasMany
    {
        return $this->hasMany(InboundShipment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Draft</span>',
            'submitted' => '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Submitted</span>',
            'approved' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Approved</span>',
            'confirmed' => '<span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Confirmed</span>',
            'partial_received' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Partial</span>',
            'received' => '<span class="px-2 py-1 bg-teal-100 text-teal-700 rounded-full text-xs font-medium">Received</span>',
            'completed' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Unknown</span>';
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        $badges = [
            'unpaid' => '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Unpaid</span>',
            'partial' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Partial</span>',
            'paid' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Paid</span>',
        ];

        return $badges[$this->payment_status] ?? '<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">Unknown</span>';
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getPaymentProgressAttribute(): float
    {
        if ($this->total_amount <= 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', '!=', 'paid');
    }

    // Helper Methods
    public static function generatePoNumber(): string
    {
        $lastPO = static::latest('id')->first();
        $nextNumber = $lastPO ? ((int) substr($lastPO->po_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function canEdit(): bool
    {
        return in_array($this->status, ['draft', 'submitted']);
    }

    public function canDelete(): bool
    {
        return $this->status === 'draft';
    }

    public function canApprove(): bool
    {
        return $this->status === 'submitted';
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function hasShipments(): bool
    {
        return $this->inboundShipments()->count() > 0;
    }

    public function getTotalShipmentsAttribute(): int
    {
        return $this->inboundShipments()->count();
    }

    public function getCompletedShipmentsAttribute(): int
    {
        return $this->inboundShipments()->where('status', 'completed')->count();
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'submitted']);
    }

    public function isDeletable(): bool
    {
        return $this->status === 'draft';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'submitted';
    }

    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function canBeCompleted(): bool
    {
        return $this->items()
            ->whereColumn('quantity_received', '<', 'quantity_ordered')
            ->count() === 0;
    }

    public function goodReceivings(): HasMany
    {
        return $this->hasMany(GoodReceiving::class);
    }   
}