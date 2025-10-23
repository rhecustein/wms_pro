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
        'status',
        'priority',
        'picking_strategy', // FEFO, FIFO, LIFO
        'assigned_to',
        'started_at',
        'completed_at',
        'total_items',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'picking_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // RELASI
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

      // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                         </span>',
            'assigned' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-user-check mr-1"></i>Assigned
                           </span>',
            'in_progress' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
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
            'urgent' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                           <i class="fas fa-exclamation-triangle mr-1"></i>Urgent
                         </span>',
            'high' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-orange-100 text-orange-800">
                         <i class="fas fa-arrow-up mr-1"></i>High
                       </span>',
            'medium' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                           <i class="fas fa-minus mr-1"></i>Medium
                         </span>',
            'low' => '<span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                        <i class="fas fa-arrow-down mr-1"></i>Low
                      </span>',
        ];

        return $badges[$this->priority] ?? $badges['medium'];
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_items === 0) return 0;
        
        $pickedItems = $this->items()->where('status', 'picked')->count();
        return round(($pickedItems / $this->total_items) * 100, 2);
    }

    // Static method to generate picking number
    public static function generatePickingNumber()
    {
        $latest = self::withTrashed()->latest('id')->first();
        $number = $latest ? intval(substr($latest->picking_number, 5)) + 1 : 1;
        return 'PICK-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}