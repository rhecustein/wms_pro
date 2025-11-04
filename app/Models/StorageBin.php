<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageBin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Relations
        'warehouse_id',
        'storage_area_id',
        'customer_id',
        
        // Location identifiers
        'code',
        'aisle',
        'row',
        'column',
        'level',
        
        // Status & Type
        'status',
        'bin_type',
        'packaging_restriction',
        
        // Capacity - Weight
        'max_weight_kg',
        'current_weight_kg',
        
        // Capacity - Volume
        'max_volume_cbm',
        'current_volume_cbm',
        
        // Capacity - Quantity
        'current_quantity',
        'min_quantity',
        'max_quantity',
        
        // Physical dimensions
        'bin_length_cm',
        'bin_width_cm',
        'bin_height_cm',
        
        // Flags
        'is_occupied',
        'is_hazmat',
        'is_active',
        'is_temperature_controlled',
        'is_locked',
        
        // Temperature control
        'min_temperature_c',
        'max_temperature_c',
        
        // Additional info
        'picking_priority',
        'barcode',
        'rfid_tag',
        'notes',
        
        // Audit fields
        'last_count_date',
        'last_count_by',
        'last_movement_date',
    ];

    protected $casts = [
        // Capacity
        'max_weight_kg' => 'decimal:2',
        'current_weight_kg' => 'decimal:2',
        'max_volume_cbm' => 'decimal:3',
        'current_volume_cbm' => 'decimal:3',
        'current_quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'max_quantity' => 'decimal:2',
        
        // Physical dimensions
        'bin_length_cm' => 'decimal:2',
        'bin_width_cm' => 'decimal:2',
        'bin_height_cm' => 'decimal:2',
        
        // Temperature
        'min_temperature_c' => 'decimal:2',
        'max_temperature_c' => 'decimal:2',
        
        // Flags
        'is_occupied' => 'boolean',
        'is_hazmat' => 'boolean',
        'is_active' => 'boolean',
        'is_temperature_controlled' => 'boolean',
        'is_locked' => 'boolean',
        
        // Priority
        'picking_priority' => 'integer',
        
        // Dates
        'last_count_date' => 'datetime',
        'last_movement_date' => 'datetime',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function storageArea()
    {
        return $this->belongsTo(StorageArea::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lastCountBy()
    {
        return $this->belongsTo(User::class, 'last_count_by');
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function stockMovementsFrom()
    {
        return $this->hasMany(StockMovement::class, 'from_bin_id');
    }

    public function stockMovementsTo()
    {
        return $this->hasMany(StockMovement::class, 'to_bin_id');
    }

    public function pallets()
    {
        return $this->hasMany(Pallet::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByStorageArea($query, $storageAreaId)
    {
        return $query->where('storage_area_id', $storageAreaId);
    }

    public function scopeByBinType($query, $binType)
    {
        return $query->where('bin_type', $binType);
    }

    public function scopeHazmat($query)
    {
        return $query->where('is_hazmat', true);
    }

    public function scopeTemperatureControlled($query)
    {
        return $query->where('is_temperature_controlled', true);
    }

    // Accessors
    public function getFullLocationAttribute()
    {
        return "{$this->aisle}-{$this->row}-{$this->column}-{$this->level}";
    }

    public function getWeightUtilizationPercentageAttribute()
    {
        if (!$this->max_weight_kg || $this->max_weight_kg == 0) {
            return 0;
        }
        return min(($this->current_weight_kg / $this->max_weight_kg) * 100, 100);
    }

    public function getVolumeUtilizationPercentageAttribute()
    {
        if (!$this->max_volume_cbm || $this->max_volume_cbm == 0) {
            return 0;
        }
        return min(($this->current_volume_cbm / $this->max_volume_cbm) * 100, 100);
    }

    public function getQuantityUtilizationPercentageAttribute()
    {
        if (!$this->max_quantity || $this->max_quantity == 0) {
            return 0;
        }
        return min(($this->current_quantity / $this->max_quantity) * 100, 100);
    }

    public function getIsNearCapacityAttribute()
    {
        return $this->weight_utilization_percentage >= 80 || 
               $this->volume_utilization_percentage >= 80 || 
               $this->quantity_utilization_percentage >= 80;
    }

    public function getIsOverCapacityAttribute()
    {
        return $this->weight_utilization_percentage >= 100 || 
               $this->volume_utilization_percentage >= 100 || 
               $this->quantity_utilization_percentage >= 100;
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'available' => 'green',
            'occupied' => 'blue',
            'reserved' => 'yellow',
            'blocked' => 'red',
            'maintenance' => 'gray',
            default => 'gray',
        };
    }

    // Methods
    public function canAcceptStock($weight = 0, $volume = 0, $quantity = 0)
    {
        if (!$this->is_active || $this->is_locked) {
            return false;
        }

        if (!in_array($this->status, ['available', 'occupied'])) {
            return false;
        }

        // Check weight capacity
        if ($this->max_weight_kg && ($this->current_weight_kg + $weight) > $this->max_weight_kg) {
            return false;
        }

        // Check volume capacity
        if ($this->max_volume_cbm && ($this->current_volume_cbm + $volume) > $this->max_volume_cbm) {
            return false;
        }

        // Check quantity capacity
        if ($this->max_quantity && ($this->current_quantity + $quantity) > $this->max_quantity) {
            return false;
        }

        return true;
    }

    public function updateCurrentCapacity()
    {
        $stocks = $this->inventoryStocks()
            ->with('product')
            ->where('quantity', '>', 0)
            ->get();

        $totalQuantity = $stocks->sum('quantity');
        
        $totalWeight = $stocks->sum(function($stock) {
            return ($stock->product->weight_kg ?? 0) * $stock->quantity;
        });
        
        $totalVolume = $stocks->sum(function($stock) {
            return ($stock->product->volume_cbm ?? 0) * $stock->quantity;
        });

        $this->update([
            'current_quantity' => $totalQuantity,
            'current_weight_kg' => $totalWeight,
            'current_volume_cbm' => $totalVolume,
            'is_occupied' => $totalQuantity > 0,
            'status' => $totalQuantity > 0 ? 'occupied' : 'available',
            'last_movement_date' => now(),
        ]);

        return $this;
    }
}