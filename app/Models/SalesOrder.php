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

    /**
     * Relationships
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed', 'picking', 'packing']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['delivered']);
    }

    /**
     * Generate SO Number
     */
    public static function generateSONumber()
    {
        $date = now();
        $prefix = 'SO-' . $date->format('Ymd') . '-';
        
        $lastSO = self::whereDate('created_at', $date)
            ->where('so_number', 'like', $prefix . '%')
            ->orderBy('so_number', 'desc')
            ->first();

        if ($lastSO) {
            $lastNumber = intval(substr($lastSO->so_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Status Check Methods
     */
    public function canEdit()
    {
        return in_array($this->status, ['draft']);
    }

    public function canDelete()
    {
        return in_array($this->status, ['draft', 'cancelled']);
    }

    public function canConfirm()
    {
        return $this->status === 'draft';
    }

    public function canCancel()
    {
        return in_array($this->status, ['draft', 'confirmed']);
    }

    public function canGeneratePicking()
    {
        return $this->status === 'confirmed';
    }

    public function canPack()
    {
        return $this->status === 'picking';
    }

    public function canShip()
    {
        return $this->status === 'packing';
    }

    public function canDeliver()
    {
        return $this->status === 'shipped';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'picking' => 'bg-yellow-100 text-yellow-800',
            'packing' => 'bg-orange-100 text-orange-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'partial' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
        ];

        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'confirmed' => 'Dikonfirmasi',
            'picking' => 'Picking',
            'packing' => 'Packing',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Belum Dibayar',
            'partial' => 'Dibayar Sebagian',
            'paid' => 'Lunas',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Get remaining payment
     */
    public function getRemainingPaymentAttribute()
    {
        return $this->total_amount;
    }

    /**
     * Get payment percentage
     */
    public function getPaymentPercentageAttribute()
    {
        if ($this->payment_status === 'paid') {
            return 100;
        } elseif ($this->payment_status === 'partial') {
            return 50; // You can adjust this or calculate from payments table
        }
        return 0;
    }

    public function pickingOrders()
    {
        return $this->hasMany(PickingOrder::class);
    }
}