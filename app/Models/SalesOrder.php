<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'so_number',
        'warehouse_id',
        'customer_id',
        'order_date',
        'requested_delivery_date',
        'status',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'total_amount',
        'currency',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_postal_code',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'requested_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function packingOrders()
    {
        return $this->hasMany(PackingOrder::class);
    }

    public function pickingOrders()
    {
        return $this->hasMany(PickingOrder::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

     // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"><i class="fas fa-file-alt mr-1"></i>Draft</span>',
            'confirmed' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"><i class="fas fa-check-circle mr-1"></i>Confirmed</span>',
            'picking' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"><i class="fas fa-hand-paper mr-1"></i>Picking</span>',
            'packing' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800"><i class="fas fa-box mr-1"></i>Packing</span>',
            'shipped' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800"><i class="fas fa-shipping-fast mr-1"></i>Shipped</span>',
            'delivered' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><i class="fas fa-check-double mr-1"></i>Delivered</span>',
            'cancelled' => '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 rounded text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>',
            'partial' => '<span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">Partial</span>',
            'paid' => '<span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Paid</span>',
        ];

        return $badges[$this->payment_status] ?? $this->payment_status;
    }

    // Methods
    public static function generateSONumber()
    {
        $lastSO = self::withTrashed()
            ->whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSO) {
            $lastNumber = intval(substr($lastSO->so_number, -5));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'SO-' . date('Y') . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function canEdit()
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }

    public function canDelete()
    {
        return $this->status === 'draft';
    }

    public function canConfirm()
    {
        return $this->status === 'draft';
    }

    public function canCancel()
    {
        return !in_array($this->status, ['delivered', 'cancelled']);
    }

    public function canGeneratePicking()
    {
        return $this->status === 'confirmed';
    }
}