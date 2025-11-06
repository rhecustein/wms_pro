<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'picking_order_id',
        'sales_order_item_id',
        'product_id',
        'storage_bin_id',
        'batch_number',
        'serial_number',
        'expiry_date',
        'quantity_requested',
        'quantity_picked',
        'unit_of_measure',
        'pick_sequence',
        'status',
        'picked_by',
        'picked_at',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'picked_at' => 'datetime',
        'quantity_requested' => 'integer',
        'quantity_picked' => 'integer',
        'pick_sequence' => 'integer',
    ];

    protected $dates = [
        'expiry_date',
        'picked_at',
        'created_at',
        'updated_at',
    ];

    // RELATIONSHIPS
    public function pickingOrder()
    {
        return $this->belongsTo(PickingOrder::class);
    }

    public function salesOrderItem()
    {
        return $this->belongsTo(SalesOrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function pickedBy()
    {
        return $this->belongsTo(User::class, 'picked_by');
    }

    // ACCESSORS
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i>Pending
                         </span>',
            'picked' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                           <i class="fas fa-check mr-1"></i>Picked
                        </span>',
            'short' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                          <i class="fas fa-exclamation mr-1"></i>Short
                       </span>',
            'cancelled' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                              <i class="fas fa-times mr-1"></i>Cancelled
                           </span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function getShortQuantityAttribute()
    {
        return $this->quantity_requested - $this->quantity_picked;
    }

    public function getIsShortPickAttribute()
    {
        return $this->quantity_picked > 0 && $this->quantity_picked < $this->quantity_requested;
    }

    public function getIsFullyPickedAttribute()
    {
        return $this->quantity_picked >= $this->quantity_requested;
    }

    public function getPickPercentageAttribute()
    {
        if ($this->quantity_requested == 0) {
            return 0;
        }
        return round(($this->quantity_picked / $this->quantity_requested) * 100, 2);
    }

    // SCOPES
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePicked($query)
    {
        return $query->where('status', 'picked');
    }

    public function scopeShort($query)
    {
        return $query->where('status', 'short');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByStorageBin($query, $binId)
    {
        return $query->where('storage_bin_id', $binId);
    }

    public function scopeByPickSequence($query)
    {
        return $query->orderBy('pick_sequence');
    }

    public function scopeWithBatch($query, $batchNumber)
    {
        return $query->where('batch_number', $batchNumber);
    }

    public function scopeWithSerial($query, $serialNumber)
    {
        return $query->where('serial_number', $serialNumber);
    }

    // HELPER METHODS
    public function canBePicked()
    {
        return $this->status === 'pending';
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['pending']);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPicked()
    {
        return $this->status === 'picked';
    }

    public function isShort()
    {
        return $this->status === 'short';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function markAsPicked($quantity, $userId = null)
    {
        $this->update([
            'quantity_picked' => $quantity,
            'status' => $quantity >= $this->quantity_requested ? 'picked' : 'short',
            'picked_by' => $userId ?? auth()->id(),
            'picked_at' => now(),
        ]);

        return $this;
    }

    public function markAsShort($quantity, $userId = null)
    {
        $this->update([
            'quantity_picked' => $quantity,
            'status' => 'short',
            'picked_by' => $userId ?? auth()->id(),
            'picked_at' => now(),
        ]);

        return $this;
    }

    public function markAsCancelled()
    {
        $this->update([
            'status' => 'cancelled',
        ]);

        return $this;
    }

    // BOOT METHOD
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->quantity_picked === null) {
                $model->quantity_picked = 0;
            }
            if ($model->status === null) {
                $model->status = 'pending';
            }
        });

        static::updated(function ($model) {
            // Update picking order total when item status changes
            if ($model->isDirty('status') || $model->isDirty('quantity_picked')) {
                $model->pickingOrder->touch();
            }
        });
    }
}