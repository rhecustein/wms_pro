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

    // Accessors
    public function getStatusBadgeAttribute()
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

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'urgent' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Urgent</span>',
            'high' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-arrow-up mr-1"></i>High</span>',
            'medium' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-minus mr-1"></i>Medium</span>',
            'low' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-arrow-down mr-1"></i>Low</span>',
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    public function getTriggerTypeBadgeAttribute()
    {
        $badges = [
            'min_level' => '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800"><i class="fas fa-level-down-alt mr-1"></i>Min Level</span>',
            'empty_pick_face' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800"><i class="fas fa-box-open mr-1"></i>Empty Pick Face</span>',
            'manual' => '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800"><i class="fas fa-hand-pointer mr-1"></i>Manual</span>',
        ];

        return $badges[$this->trigger_type] ?? $badges['manual'];
    }

    // Helper method to generate task number
    public static function generateTaskNumber()
    {
        $lastTask = self::withTrashed()->latest('id')->first();
        $number = $lastTask ? intval(substr($lastTask->task_number, 4)) + 1 : 1;
        return 'REP-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}