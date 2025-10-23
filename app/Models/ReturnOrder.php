<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_number',
        'delivery_order_id',
        'sales_order_id',
        'warehouse_id',
        'customer_id',
        'return_date',
        'return_type',
        'status',
        'total_items',
        'total_quantity',
        'inspected_by',
        'inspected_at',
        'disposition',
        'refund_amount',
        'refund_status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_quantity' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'inspected_at' => 'datetime',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function inspectedByUser()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnOrderItem::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'received' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-box mr-1"></i>Received</span>',
            'inspected' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-search mr-1"></i>Inspected</span>',
            'restocked' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Restocked</span>',
            'disposed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-trash mr-1"></i>Disposed</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-times mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getReturnTypeBadgeAttribute()
    {
        $badges = [
            'customer_return' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-undo mr-1"></i>Customer Return</span>',
            'damaged' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-triangle mr-1"></i>Damaged</span>',
            'expired' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-calendar-times mr-1"></i>Expired</span>',
            'wrong_item' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-exchange-alt mr-1"></i>Wrong Item</span>',
        ];

        return $badges[$this->return_type] ?? $this->return_type;
    }
}