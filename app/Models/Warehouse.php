<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'manager_id',
        'total_area_sqm',
        'storage_capacity_cbm',
        'height_meters',
        'number_of_docks',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'total_area_sqm' => 'decimal:2',
        'storage_capacity_cbm' => 'decimal:2',
        'height_meters' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // ACTIVITY LOG CONFIGURATION
    // ==========================================
    
    /**
     * Configure activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'code',
                'name',
                'address',
                'city',
                'province',
                'manager_id',
                'is_active'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Warehouse has been {$eventName}")
            ->useLogName('warehouse');
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    /**
     * Warehouse manager
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * User who created this warehouse
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this warehouse
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Storage areas in this warehouse
     */
    public function storageAreas()
    {
        return $this->hasMany(StorageArea::class);
    }

    /**
     * Storage bins in this warehouse
     */
    public function storageBins()
    {
        return $this->hasMany(StorageBin::class);
    }

    /**
     * Sales orders from this warehouse
     */
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    /**
     * Purchase orders for this warehouse
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Transfer orders from this warehouse
     */
    public function transfersFrom()
    {
        return $this->hasMany(TransferOrder::class, 'from_warehouse_id');
    }

    /**
     * Transfer orders to this warehouse
     */
    public function transfersTo()
    {
        return $this->hasMany(TransferOrder::class, 'to_warehouse_id');
    }

    /**
     * Inbound shipments to this warehouse
     */
    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    /**
     * Good receivings at this warehouse
     */
    public function goodReceivings()
    {
        return $this->hasMany(GoodReceiving::class);
    }

    /**
     * Picking orders from this warehouse
     */
    public function pickingOrders()
    {
        return $this->hasMany(PickingOrder::class);
    }

    /**
     * Packing orders from this warehouse
     */
    public function packingOrders()
    {
        return $this->hasMany(PackingOrder::class);
    }

    /**
     * Delivery orders from this warehouse
     */
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    /**
     * Return orders to this warehouse
     */
    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class);
    }

    /**
     * Inventory stocks in this warehouse
     */
    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    /**
     * Pallets in this warehouse
     */
    public function pallets()
    {
        return $this->hasMany(Pallet::class);
    }

    // ==========================================
    // ACCESSORS & MUTATORS
    // ==========================================
    
    /**
     * Get warehouse utilization percentage
     */
    public function getUtilizationAttribute()
    {
        $totalBins = $this->storageBins()->count();
        
        if ($totalBins === 0) {
            return 0;
        }

        $occupiedBins = $this->storageBins()->where('status', 'occupied')->count();
        
        return round(($occupiedBins / $totalBins) * 100, 2);
    }

    /**
     * Get formatted address
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->province,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    /**
     * Scope for active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive warehouses
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for warehouses by city
     */
    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope for warehouses by province
     */
    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }
}