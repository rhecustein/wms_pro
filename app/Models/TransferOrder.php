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
}