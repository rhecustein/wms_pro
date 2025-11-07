<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'do_number',
        'sales_order_id',
        'packing_order_id',
        'warehouse_id',
        'customer_id',
        'delivery_date',
        'vehicle_id',
        'driver_id',
        'status',
        'total_boxes',
        'total_weight_kg',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'loaded_at',
        'departed_at',
        'delivered_at',
        'received_by_name',
        'received_by_signature',
        'delivery_proof_image',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_weight_kg' => 'decimal:2',
        'loaded_at' => 'datetime',
        'departed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function packingOrder()
    {
        return $this->belongsTo(PackingOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'prepared' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-box mr-1"></i>Prepared</span>',
            'loaded' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-truck-loading mr-1"></i>Loaded</span>',
            'in_transit' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-shipping-fast mr-1"></i>In Transit</span>',
            'delivered' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Delivered</span>',
            'returned' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-undo mr-1"></i>Returned</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
    }

    // Generate DO Number
    public static function generateDoNumber()
    {
        $lastDo = self::withTrashed()->orderBy('id', 'desc')->first();
        $lastNumber = $lastDo ? intval(substr($lastDo->do_number, 3)) : 0;
        $newNumber = $lastNumber + 1;
        
        return 'DO-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}