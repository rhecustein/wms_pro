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

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'assigned' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-user-check mr-1"></i>Assigned</span>',
            'in_progress' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-spinner mr-1"></i>In Progress</span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function getPriorityBadgeAttribute(): string
    {
        $badges = [
            'high' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>High</span>',
            'medium' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-minus-circle mr-1"></i>Medium</span>',
            'low' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-arrow-down mr-1"></i>Low</span>',
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }
}