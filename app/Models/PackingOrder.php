<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackingOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'packing_number',
        'picking_order_id',
        'sales_order_id',
        'warehouse_id',
        'packing_date',
        'status',
        'assigned_to',
        'started_at',
        'completed_at',
        'total_boxes',
        'total_weight_kg',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'packing_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_weight_kg' => 'decimal:2',
    ];

    // Relationships
    public function pickingOrder()
    {
        return $this->belongsTo(PickingOrder::class);
    }

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
        return $this->hasMany(PackingOrderItem::class);
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
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Pending</span>',
            'in_progress' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-spinner mr-1"></i>In Progress</span>',
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Completed</span>',
            'cancelled' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Cancelled</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }
}