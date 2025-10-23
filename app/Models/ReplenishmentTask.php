<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReplenishmentTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_number',
        'warehouse_id',
        'product_id',
        'from_storage_bin_id',
        'to_storage_bin_id',
        'batch_number',
        'serial_number',
        'quantity_suggested',
        'quantity_moved',
        'unit_of_measure',
        'priority',
        'status',
        'trigger_type',
        'assigned_to',
        'assigned_at',
        'started_at',
        'completed_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity_suggested' => 'decimal:2',
        'quantity_moved' => 'decimal:2',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromStorageBin()
    {
        return $this->belongsTo(StorageBin::class, 'from_storage_bin_id');
    }

    public function toStorageBin()
    {
        return $this->belongsTo(StorageBin::class, 'to_storage_bin_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}