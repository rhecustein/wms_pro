<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-file-alt mr-1"></i>Draft</span>',
            'submitted' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-paper-plane mr-1"></i>Submitted</span>',
            'approved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800"><i class="fas fa-thumbs-up mr-1"></i>Approved</span>',
            'confirmed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-check-circle mr-1"></i>Confirmed</span>',
            'partial_received' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-hourglass-half mr-1"></i>Partial Received</span>',
            'received' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800"><i class="fas fa-box-open mr-1"></i>Received</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-double mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'unpaid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Unpaid</span>',
            'partial' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-coins mr-1"></i>Partial</span>',
            'paid' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Paid</span>',
        ];

        return $badges[$this->payment_status] ?? $badges['unpaid'];
    }

    public function getRemainingPaymentAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getTotalReceivedItemsAttribute()
    {
        return $this->items()->sum('quantity_received');
    }

    public function getTotalOrderedItemsAttribute()
    {
        return $this->items()->sum('quantity_ordered');
    }

    public function getReceivingProgressAttribute()
    {
        $total = $this->total_ordered_items;
        if ($total == 0) return 0;
        
        return min(100, round(($this->total_received_items / $total) * 100, 1));
    }

    // Generate PO Number
    public static function generatePoNumber()
    {
        $lastPo = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $lastPo ? intval(substr($lastPo->po_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'approved']);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', '!=', 'paid');
    }
}