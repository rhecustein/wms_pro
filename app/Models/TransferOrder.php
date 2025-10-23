<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transfer_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_type',
        'transfer_date',
        'expected_arrival_date',
        'status',
        'total_items',
        'total_quantity',
        'vehicle_id',
        'driver_id',
        'approved_by',
        'approved_at',
        'shipped_at',
        'received_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'expected_arrival_date' => 'date',
        'total_quantity' => 'decimal:2',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(TransferOrderItem::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-file-alt mr-1"></i>Draft</span>',
            'approved' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-check mr-1"></i>Approved</span>',
            'in_transit' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-truck mr-1"></i>In Transit</span>',
            'received' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-inbox mr-1"></i>Received</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    public function getTransferTypeBadgeAttribute()
    {
        $badges = [
            'inter_warehouse' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800"><i class="fas fa-exchange-alt mr-1"></i>Inter Warehouse</span>',
            'internal_bin' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-teal-100 text-teal-800"><i class="fas fa-layer-group mr-1"></i>Internal Bin</span>',
            'consolidation' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-compress mr-1"></i>Consolidation</span>',
        ];

        return $badges[$this->transfer_type] ?? $badges['inter_warehouse'];
    }

    // Generate transfer number
    public static function generateTransferNumber()
    {
        $lastTransfer = self::withTrashed()->latest('id')->first();
        $number = $lastTransfer ? intval(substr($lastTransfer->transfer_number, 4)) + 1 : 1;
        return 'TRF-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}