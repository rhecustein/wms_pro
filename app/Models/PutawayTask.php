<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PutawayTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_number',
        'good_receiving_id',
        'warehouse_id',
        'product_id',
        'batch_number',
        'serial_number',
        'quantity',
        'unit_of_measure',
        'from_location',
        'to_storage_bin_id',
        'pallet_id',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'started_at',
        'completed_at',
        'suggested_by_system',
        'packaging_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'suggested_by_system' => 'boolean',
    ];

    public function goodReceiving()
    {
        return $this->belongsTo(GoodReceiving::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function toStorageBin()
    {
        return $this->belongsTo(StorageBin::class, 'to_storage_bin_id');
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}