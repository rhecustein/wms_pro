<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'product_id',
        'quantity_on_hand',
        'quantity_available',
        'quantity_reserved',
        'quantity_allocated',
        'quantity_in_transit',
        'quantity_damaged',
        'quantity_quarantine',
        'unit_id',
        'batch_number',
        'lot_number',
        'serial_number',
        'manufacturing_date',
        'expiry_date',
        'best_before_date',
        'unit_cost',
        'total_cost',
        'costing_method',
        'stock_status',
        'quality_status',
        'weight',
        'volume',
        'pallet_count',
        'supplier_id',
        'supplier_batch_number',
        'last_received_at',
        'last_picked_at',
        'last_counted_at',
        'last_adjusted_at',
        'cycle_count_frequency',
        'next_count_date',
        'count_variance',
        'is_below_minimum',
        'is_near_expiry',
        'is_expired',
        'requires_attention',
        'bin_location',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity_on_hand' => 'decimal:2',
        'quantity_available' => 'decimal:2',
        'quantity_reserved' => 'decimal:2',
        'quantity_allocated' => 'decimal:2',
        'quantity_in_transit' => 'decimal:2',
        'quantity_damaged' => 'decimal:2',
        'quantity_quarantine' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'volume' => 'decimal:2',
        'pallet_count' => 'integer',
        'cycle_count_frequency' => 'integer',
        'count_variance' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'best_before_date' => 'date',
        'next_count_date' => 'date',
        'last_received_at' => 'datetime',
        'last_picked_at' => 'datetime',
        'last_counted_at' => 'datetime',
        'last_adjusted_at' => 'datetime',
        'is_below_minimum' => 'boolean',
        'is_near_expiry' => 'boolean',
        'is_expired' => 'boolean',
        'requires_attention' => 'boolean',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Accessors
    public function getStockStatusBadgeAttribute()
    {
        $badges = [
            'in_stock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>In Stock</span>',
            'low_stock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i>Low Stock</span>',
            'out_of_stock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Out of Stock</span>',
            'overstock' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-layer-group mr-1"></i>Overstock</span>',
            'discontinued' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"><i class="fas fa-ban mr-1"></i>Discontinued</span>',
        ];

        return $badges[$this->stock_status] ?? $badges['in_stock'];
    }

    public function getQualityStatusBadgeAttribute()
    {
        $badges = [
            'good' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Good</span>',
            'damaged' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Damaged</span>',
            'expired' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-calendar-times mr-1"></i>Expired</span>',
            'quarantine' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-shield-alt mr-1"></i>Quarantine</span>',
            'returned' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-undo mr-1"></i>Returned</span>',
        ];

        return $badges[$this->quality_status] ?? $badges['good'];
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) return null;
        
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiry_date) return false;
        
        return Carbon::now()->greaterThan($this->expiry_date);
    }

    public function getIsNearExpiryAttribute()
    {
        if (!$this->expiry_date || $this->is_expired) return false;
        
        return $this->days_until_expiry <= 30 && $this->days_until_expiry > 0;
    }

    public function getStockValueAttribute()
    {
        return $this->quantity_on_hand * $this->unit_cost;
    }

    public function getAvailableValueAttribute()
    {
        return $this->quantity_available * $this->unit_cost;
    }

    public function getStockHealthScoreAttribute()
    {
        $score = 100;

        // Deduct for low stock
        if ($this->is_below_minimum) $score -= 30;

        // Deduct for near expiry
        if ($this->is_near_expiry) $score -= 20;

        // Deduct for expired
        if ($this->is_expired) $score -= 50;

        // Deduct for damaged
        if ($this->quantity_damaged > 0) {
            $damageRate = ($this->quantity_damaged / $this->quantity_on_hand) * 100;
            $score -= min(20, $damageRate);
        }

        // Deduct for long time since count
        if ($this->last_counted_at) {
            $daysSinceCount = Carbon::now()->diffInDays($this->last_counted_at);
            if ($daysSinceCount > 90) $score -= 10;
        }

        return max(0, round($score, 1));
    }

    public function getTurnoverRateAttribute()
    {
        // Calculate based on last 30 days activity
        // This is a simplified calculation
        if (!$this->last_picked_at || !$this->last_received_at) return 0;

        $days = max(1, Carbon::parse($this->last_received_at)->diffInDays($this->last_picked_at));
        
        return $this->quantity_on_hand > 0 ? round(30 / $days, 2) : 0;
    }

    // Scopes
    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity_on_hand', '>', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity_available', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('is_below_minimum', true)
                    ->orWhere('stock_status', 'low_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_on_hand', '<=', 0)
                    ->orWhere('stock_status', 'out_of_stock');
    }

    public function scopeNearExpiry($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '>', Carbon::now())
                    ->where('expiry_date', '<=', Carbon::now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', Carbon::now());
    }

    public function scopeDamaged($query)
    {
        return $query->where('quantity_damaged', '>', 0)
                    ->orWhere('quality_status', 'damaged');
    }

    public function scopeQuarantine($query)
    {
        return $query->where('quantity_quarantine', '>', 0)
                    ->orWhere('quality_status', 'quarantine');
    }

    public function scopeRequiresCount($query)
    {
        return $query->whereNotNull('next_count_date')
                    ->where('next_count_date', '<=', Carbon::now());
    }

    public function scopeByBatch($query, $batchNumber)
    {
        return $query->where('batch_number', $batchNumber);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeGoodQuality($query)
    {
        return $query->where('quality_status', 'good');
    }

    public function scopeRequiresAttention($query)
    {
        return $query->where('requires_attention', true);
    }

    // Methods
    public function adjustQuantity($quantity, $reason = null, $userId = null)
    {
        $oldQuantity = $this->quantity_on_hand;
        $this->quantity_on_hand += $quantity;
        $this->quantity_available = max(0, $this->quantity_on_hand - $this->quantity_reserved - $this->quantity_allocated);
        $this->last_adjusted_at = Carbon::now();
        
        if ($userId) {
            $this->updated_by = $userId;
        }

        $this->updateStockStatus();
        $this->save();

        // Log stock movement
        StockMovement::create([
            'inventory_id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'type' => $quantity > 0 ? 'adjustment_in' : 'adjustment_out',
            'quantity' => abs($quantity),
            'quantity_before' => $oldQuantity,
            'quantity_after' => $this->quantity_on_hand,
            'unit_id' => $this->unit_id,
            'reason' => $reason,
            'created_by' => $userId,
        ]);

        return $this;
    }

    public function reserve($quantity, $orderId = null)
    {
        if ($this->quantity_available < $quantity) {
            throw new \Exception('Insufficient available quantity');
        }

        $this->quantity_reserved += $quantity;
        $this->quantity_available -= $quantity;
        $this->save();

        return $this;
    }

    public function release($quantity)
    {
        $releaseQty = min($quantity, $this->quantity_reserved);
        $this->quantity_reserved -= $releaseQty;
        $this->quantity_available += $releaseQty;
        $this->save();

        return $this;
    }

    public function allocate($quantity)
    {
        if ($this->quantity_reserved < $quantity) {
            throw new \Exception('Insufficient reserved quantity');
        }

        $this->quantity_reserved -= $quantity;
        $this->quantity_allocated += $quantity;
        $this->save();

        return $this;
    }

    public function pick($quantity, $userId = null)
    {
        if ($this->quantity_allocated < $quantity && $this->quantity_available < $quantity) {
            throw new \Exception('Insufficient quantity to pick');
        }

        $oldQuantity = $this->quantity_on_hand;
        
        if ($this->quantity_allocated >= $quantity) {
            $this->quantity_allocated -= $quantity;
        } else {
            $this->quantity_available -= $quantity;
        }

        $this->quantity_on_hand -= $quantity;
        $this->last_picked_at = Carbon::now();
        
        $this->updateStockStatus();
        $this->save();

        // Log stock movement
        StockMovement::create([
            'inventory_id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'type' => 'pick',
            'quantity' => $quantity,
            'quantity_before' => $oldQuantity,
            'quantity_after' => $this->quantity_on_hand,
            'unit_id' => $this->unit_id,
            'created_by' => $userId,
        ]);

        return $this;
    }

    public function receive($quantity, $unitCost = null, $userId = null)
    {
        $oldQuantity = $this->quantity_on_hand;
        $this->quantity_on_hand += $quantity;
        $this->quantity_available += $quantity;
        $this->last_received_at = Carbon::now();

        if ($unitCost !== null) {
            $this->updateCost($quantity, $unitCost);
        }

        $this->updateStockStatus();
        $this->save();

        // Log stock movement
        StockMovement::create([
            'inventory_id' => $this->id,
            'warehouse_id' => $this->warehouse_id,
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'type' => 'receive',
            'quantity' => $quantity,
            'quantity_before' => $oldQuantity,
            'quantity_after' => $this->quantity_on_hand,
            'unit_id' => $this->unit_id,
            'unit_cost' => $unitCost,
            'created_by' => $userId,
        ]);

        return $this;
    }

    public function updateCost($newQuantity, $newUnitCost)
    {
        // Calculate weighted average cost
        $totalValue = ($this->quantity_on_hand * $this->unit_cost) + ($newQuantity * $newUnitCost);
        $totalQuantity = $this->quantity_on_hand + $newQuantity;

        if ($totalQuantity > 0) {
            $this->unit_cost = $totalValue / $totalQuantity;
            $this->total_cost = $this->quantity_on_hand * $this->unit_cost;
        }

        return $this;
    }

    public function updateStockStatus()
    {
        $product = $this->product;

        if ($this->quantity_on_hand <= 0) {
            $this->stock_status = 'out_of_stock';
            $this->is_below_minimum = true;
        } elseif ($product && $this->quantity_on_hand <= $product->minimum_stock) {
            $this->stock_status = 'low_stock';
            $this->is_below_minimum = true;
        } elseif ($product && $product->maximum_stock > 0 && $this->quantity_on_hand >= $product->maximum_stock) {
            $this->stock_status = 'overstock';
            $this->is_below_minimum = false;
        } else {
            $this->stock_status = 'in_stock';
            $this->is_below_minimum = false;
        }

        // Check expiry
        if ($this->expiry_date) {
            $this->is_expired = Carbon::now()->greaterThan($this->expiry_date);
            $this->is_near_expiry = !$this->is_expired && 
                                   Carbon::now()->diffInDays($this->expiry_date, false) <= 30;
        }

        // Set requires attention flag
        $this->requires_attention = $this->is_below_minimum || 
                                   $this->is_near_expiry || 
                                   $this->is_expired ||
                                   $this->quantity_damaged > 0;

        return $this;
    }

    public function scheduleNextCount()
    {
        if ($this->last_counted_at) {
            $this->next_count_date = Carbon::parse($this->last_counted_at)
                ->addDays($this->cycle_count_frequency);
        } else {
            $this->next_count_date = Carbon::now()->addDays($this->cycle_count_frequency);
        }

        $this->save();

        return $this;
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventory) {
            $inventory->updateStockStatus();
            
            if (!$inventory->next_count_date) {
                $inventory->next_count_date = Carbon::now()->addDays($inventory->cycle_count_frequency);
            }
        });

        static::updating(function ($inventory) {
            $inventory->total_cost = $inventory->quantity_on_hand * $inventory->unit_cost;
        });
    }
}