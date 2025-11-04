<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'code',
        'name',
        'description',
        'zone',
        'aisle',
        'rack',
        'shelf',
        'bin',
        'level',
        'type',
        'temperature_zone',
        'is_hazmat',
        'requires_certification',
        'max_weight',
        'max_volume',
        'max_pallets',
        'length',
        'width',
        'height',
        'current_weight',
        'current_volume',
        'current_pallets',
        'occupancy_rate',
        'access_type',
        'pick_priority',
        'is_pick_face',
        'is_active',
        'is_available',
        'is_mixed_products',
        'status',
        'blocked_reason',
        'barcode',
        'qr_code',
        'latitude',
        'longitude',
        'parent_location_id',
        'notes',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_hazmat' => 'boolean',
        'requires_certification' => 'boolean',
        'max_weight' => 'decimal:2',
        'max_volume' => 'decimal:2',
        'max_pallets' => 'integer',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'current_weight' => 'decimal:2',
        'current_volume' => 'decimal:2',
        'current_pallets' => 'integer',
        'occupancy_rate' => 'decimal:2',
        'pick_priority' => 'integer',
        'is_pick_face' => 'boolean',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'is_mixed_products' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function parentLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_location_id');
    }

    public function childLocations()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'location_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'location_id');
    }

    public function inboundShipmentItems()
    {
        return $this->hasMany(InboundShipmentItem::class, 'location_id');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'available' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Available</span>',
            'occupied' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-box mr-1"></i>Occupied</span>',
            'reserved' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"><i class="fas fa-lock mr-1"></i>Reserved</span>',
            'maintenance' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-tools mr-1"></i>Maintenance</span>',
            'blocked' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-ban mr-1"></i>Blocked</span>',
        ];

        return $badges[$this->status] ?? $badges['available'];
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'storage' => '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"><i class="fas fa-warehouse mr-1"></i>Storage</span>',
            'picking' => '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700"><i class="fas fa-hand-pointer mr-1"></i>Picking</span>',
            'receiving' => '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700"><i class="fas fa-truck-loading mr-1"></i>Receiving</span>',
            'shipping' => '<span class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700"><i class="fas fa-shipping-fast mr-1"></i>Shipping</span>',
            'staging' => '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700"><i class="fas fa-dolly mr-1"></i>Staging</span>',
            'quarantine' => '<span class="px-2 py-1 text-xs rounded bg-orange-100 text-orange-700"><i class="fas fa-exclamation-triangle mr-1"></i>Quarantine</span>',
            'return' => '<span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-700"><i class="fas fa-undo mr-1"></i>Return</span>',
            'damaged' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700"><i class="fas fa-exclamation-circle mr-1"></i>Damaged</span>',
            'packing' => '<span class="px-2 py-1 text-xs rounded bg-teal-100 text-teal-700"><i class="fas fa-box-open mr-1"></i>Packing</span>',
            'bulk' => '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700"><i class="fas fa-cubes mr-1"></i>Bulk</span>',
        ];

        return $badges[$this->type] ?? $badges['storage'];
    }

    public function getTemperatureZoneBadgeAttribute()
    {
        $badges = [
            'ambient' => '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700"><i class="fas fa-temperature-high mr-1"></i>Ambient</span>',
            'chilled' => '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700"><i class="fas fa-snowflake mr-1"></i>Chilled</span>',
            'frozen' => '<span class="px-2 py-1 text-xs rounded bg-cyan-100 text-cyan-700"><i class="fas fa-icicles mr-1"></i>Frozen</span>',
        ];

        return $badges[$this->temperature_zone] ?? $badges['ambient'];
    }

    public function getFullLocationPathAttribute()
    {
        $parts = array_filter([
            $this->zone,
            $this->aisle,
            $this->rack,
            $this->shelf,
            $this->bin,
        ]);

        return implode('-', $parts) ?: $this->code;
    }

    public function getCapacityUsageAttribute()
    {
        if (!$this->max_weight && !$this->max_volume && !$this->max_pallets) {
            return 0;
        }

        $percentages = [];

        if ($this->max_weight > 0) {
            $percentages[] = ($this->current_weight / $this->max_weight) * 100;
        }

        if ($this->max_volume > 0) {
            $percentages[] = ($this->current_volume / $this->max_volume) * 100;
        }

        if ($this->max_pallets > 0) {
            $percentages[] = ($this->current_pallets / $this->max_pallets) * 100;
        }

        return $percentages ? max($percentages) : 0;
    }

    public function getRemainingCapacityAttribute()
    {
        return [
            'weight' => max(0, $this->max_weight - $this->current_weight),
            'volume' => max(0, $this->max_volume - $this->current_volume),
            'pallets' => max(0, $this->max_pallets - $this->current_pallets),
        ];
    }

    public function getIsFullAttribute()
    {
        return $this->capacity_usage >= 100;
    }

    public function getIsNearlyFullAttribute()
    {
        return $this->capacity_usage >= 80 && $this->capacity_usage < 100;
    }

    public function getTotalAreaAttribute()
    {
        if (!$this->length || !$this->width) return null;
        
        return ($this->length * $this->width) / 10000; // Convert CM2 to M2
    }

    public function getTotalVolumeAttribute()
    {
        if (!$this->length || !$this->width || !$this->height) return null;
        
        return ($this->length * $this->width * $this->height) / 1000000; // Convert CM3 to M3
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', 'available');
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeByZone($query, $zone)
    {
        return $query->where('zone', $zone);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePickingLocations($query)
    {
        return $query->where('type', 'picking')->where('is_pick_face', true);
    }

    public function scopeStorageLocations($query)
    {
        return $query->where('type', 'storage');
    }

    public function scopeHazmat($query)
    {
        return $query->where('is_hazmat', true);
    }

    public function scopeWithCapacity($query)
    {
        return $query->whereNotNull('max_weight')
                    ->orWhereNotNull('max_volume')
                    ->orWhereNotNull('max_pallets');
    }

    public function scopeNearlyFull($query, $threshold = 80)
    {
        return $query->where('occupancy_rate', '>=', $threshold)
                    ->where('occupancy_rate', '<', 100);
    }

    public function scopeFull($query)
    {
        return $query->where('occupancy_rate', '>=', 100);
    }

    // Methods
    public function canAccommodate($weight = 0, $volume = 0, $pallets = 0)
    {
        if (!$this->is_available || $this->status !== 'available') {
            return false;
        }

        if ($this->max_weight && ($this->current_weight + $weight) > $this->max_weight) {
            return false;
        }

        if ($this->max_volume && ($this->current_volume + $volume) > $this->max_volume) {
            return false;
        }

        if ($this->max_pallets && ($this->current_pallets + $pallets) > $this->max_pallets) {
            return false;
        }

        return true;
    }

    public function updateOccupancy()
    {
        $this->occupancy_rate = $this->capacity_usage;
        
        if ($this->occupancy_rate >= 100) {
            $this->status = 'occupied';
            $this->is_available = false;
        } elseif ($this->occupancy_rate > 0) {
            $this->status = 'occupied';
            $this->is_available = $this->is_mixed_products;
        } else {
            $this->status = 'available';
            $this->is_available = true;
        }

        $this->save();
    }

    // Generate Location Code
    public static function generateLocationCode($warehouseId, $zone, $aisle, $rack, $shelf = null, $bin = null)
    {
        $parts = array_filter([$zone, $aisle, $rack, $shelf, $bin]);
        return implode('-', $parts);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($location) {
            // Auto-generate barcode if not provided
            if (!$location->barcode) {
                $location->barcode = 'LOC-' . strtoupper(uniqid());
            }
        });
    }
}