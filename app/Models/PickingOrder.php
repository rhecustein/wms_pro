<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PickingOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'picking_number',
        'sales_order_id',
        'warehouse_id',
        'picking_date',
        'picking_type',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'started_at',
        'completed_at',
        'total_items',
        'total_quantity',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'picking_date' => 'datetime',
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_items' => 'integer',
        'total_quantity' => 'integer',
    ];

    protected $dates = [
        'picking_date',
        'assigned_at',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // RELATIONSHIPS
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items()
    {
        return $this->hasMany(PickingOrderItem::class);
    }

    public function packingOrders()
    {
        return $this->hasMany(PackingOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ACCESSORS
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                         </span>',
            'assigned' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-user-check mr-1"></i>Assigned
                           </span>',
            'in_progress' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-spinner mr-1"></i>In Progress
                              </span>',
            'completed' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                              <i class="fas fa-check-circle mr-1"></i>Completed
                            </span>',
            'cancelled' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                              <i class="fas fa-times-circle mr-1"></i>Cancelled
                            </span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'urgent' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                           <i class="fas fa-exclamation-triangle mr-1"></i>Urgent
                         </span>',
            'high' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                         <i class="fas fa-arrow-up mr-1"></i>High
                       </span>',
            'medium' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                           <i class="fas fa-minus mr-1"></i>Medium
                         </span>',
            'low' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        <i class="fas fa-arrow-down mr-1"></i>Low
                      </span>',
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_items === 0) {
            return 0;
        }
        
        $pickedItems = $this->items()->where('status', 'picked')->count();
        return round(($pickedItems / $this->total_items) * 100, 2);
    }

    public function getPickedItemsCountAttribute()
    {
        return $this->items()->where('status', 'picked')->count();
    }

    public function getPendingItemsCountAttribute()
    {
        return $this->items()->where('status', 'pending')->count();
    }

    public function getShortItemsCountAttribute()
    {
        return $this->items()->where('status', 'short')->count();
    }

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopePickingType($query, $type)
    {
        return $query->where('picking_type', $type);
    }

    // STATIC METHODS
    public static function generatePickingNumber()
    {
        $latest = self::withTrashed()->latest('id')->first();
        $number = $latest ? intval(substr($latest->picking_number, 5)) + 1 : 1;
        return 'PICK-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // HELPER METHODS
    public function canBeEdited()
    {
        return in_array($this->status, ['pending', 'assigned']);
    }

    public function canBeDeleted()
    {
        return $this->status === 'pending';
    }

    public function canBeStarted()
    {
        return in_array($this->status, ['pending', 'assigned']);
    }

    public function canBeCompleted()
    {
        return $this->status === 'in_progress' 
            && $this->items()->where('status', 'pending')->count() === 0;
    }

    public function canBeCancelled()
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->picking_number)) {
                $model->picking_number = self::generatePickingNumber();
            }
        });

        static::deleting(function ($model) {
            // Soft delete related items
            $model->items()->delete();
        });
    }
}