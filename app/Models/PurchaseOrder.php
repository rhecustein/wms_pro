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
        'vendor_id',
        'po_date',
        'expected_delivery_date',
        'status',
        'payment_terms',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    public function goodReceivings()
    {
        return $this->hasMany(GoodReceiving::class);
    }

     // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-file-alt mr-1"></i>Draft</span>',
            'submitted' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-paper-plane mr-1"></i>Submitted</span>',
            'confirmed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-check-circle mr-1"></i>Confirmed</span>',
            'partial' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-hourglass-half mr-1"></i>Partial</span>',
            'completed' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-double mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    // Generate PO Number
    public static function generatePoNumber()
    {
        $lastPo = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $lastPo ? intval(substr($lastPo->po_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}