<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_number',
        'license_plate',
        'vehicle_type',
        'brand',
        'model',
        'year',
        'capacity_kg',
        'capacity_cbm',
        'status',
        'ownership',
        'last_maintenance_date',
        'next_maintenance_date',
        'odometer_km',
        'fuel_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity_kg' => 'decimal:2',
        'capacity_cbm' => 'decimal:2',
        'year' => 'integer',
        'odometer_km' => 'integer',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be appended to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_badge',
        'type_badge',
        'ownership_badge',
        'maintenance_status',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the user who created the vehicle.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the vehicle.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all transfer orders for this vehicle.
     */
    public function transferOrders()
    {
        return $this->hasMany(TransferOrder::class);
    }

    /**
     * Get all delivery orders for this vehicle.
     */
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    // ========================================
    // ACCESSORS & MUTATORS
    // ========================================

    /**
     * Get the status badge HTML.
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'available' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>',
                    'in_use' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">In Use</span>',
                    'maintenance' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Maintenance</span>',
                    'inactive' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
                ];

                return $badges[$this->status] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
            }
        );
    }

    /**
     * Get the vehicle type badge HTML.
     */
    protected function typeBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'truck' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-truck mr-1"></i>Truck</span>',
                    'van' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800"><i class="fas fa-shuttle-van mr-1"></i>Van</span>',
                    'forklift' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800"><i class="fas fa-forklift mr-1"></i>Forklift</span>',
                    'reach_truck' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800"><i class="fas fa-forklift mr-1"></i>Reach Truck</span>',
                ];

                return $badges[$this->vehicle_type] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
            }
        );
    }

    /**
     * Get the ownership badge HTML.
     */
    protected function ownershipBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $badges = [
                    'owned' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-building mr-1"></i>Owned</span>',
                    'rented' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"><i class="fas fa-handshake mr-1"></i>Rented</span>',
                    'leased' => '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800"><i class="fas fa-file-contract mr-1"></i>Leased</span>',
                ];

                return $badges[$this->ownership] ?? '<span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
            }
        );
    }

    /**
     * Get the maintenance status.
     */
    protected function maintenanceStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->next_maintenance_date) {
                    return 'not_scheduled';
                }

                $now = now();
                $nextMaintenance = $this->next_maintenance_date;

                if ($nextMaintenance < $now) {
                    return 'overdue';
                }

                if ($nextMaintenance <= $now->copy()->addDays(7)) {
                    return 'due_soon';
                }

                return 'scheduled';
            }
        );
    }

    /**
     * Get the formatted capacity KG.
     */
    protected function formattedCapacityKg(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->capacity_kg 
                ? number_format($this->capacity_kg, 2) . ' kg' 
                : '-'
        );
    }

    /**
     * Get the formatted capacity CBM.
     */
    protected function formattedCapacityCbm(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->capacity_cbm 
                ? number_format($this->capacity_cbm, 2) . ' m³' 
                : '-'
        );
    }

    /**
     * Get the formatted odometer.
     */
    protected function formattedOdometer(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->odometer_km) . ' km'
        );
    }

    /**
     * Get the vehicle type display name.
     */
    protected function vehicleTypeDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst(str_replace('_', ' ', $this->vehicle_type))
        );
    }

    /**
     * Get the status display name.
     */
    protected function statusDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst(str_replace('_', ' ', $this->status))
        );
    }

    /**
     * Get the ownership display name.
     */
    protected function ownershipDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->ownership)
        );
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope a query to only include available vehicles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include vehicles in use.
     */
    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    /**
     * Scope a query to only include vehicles in maintenance.
     */
    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * Scope a query to only include inactive vehicles.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include owned vehicles.
     */
    public function scopeOwned($query)
    {
        return $query->where('ownership', 'owned');
    }

    /**
     * Scope a query to only include rented vehicles.
     */
    public function scopeRented($query)
    {
        return $query->where('ownership', 'rented');
    }

    /**
     * Scope a query to only include leased vehicles.
     */
    public function scopeLeased($query)
    {
        return $query->where('ownership', 'leased');
    }

    /**
     * Scope a query to filter by vehicle type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('vehicle_type', $type);
    }

    /**
     * Scope a query to include vehicles with overdue maintenance.
     */
    public function scopeMaintenanceOverdue($query)
    {
        return $query->where('next_maintenance_date', '<', now())
                    ->whereNotNull('next_maintenance_date');
    }

    /**
     * Scope a query to include vehicles with maintenance due soon (within 7 days).
     */
    public function scopeMaintenanceDueSoon($query)
    {
        return $query->whereBetween('next_maintenance_date', [
            now(),
            now()->addDays(7)
        ]);
    }

    /**
     * Scope a query to search vehicles.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('vehicle_number', 'like', "%{$search}%")
              ->orWhere('license_plate', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%");
        });
    }

    // ========================================
    // STATIC METHODS
    // ========================================

    /**
     * Generate a new vehicle number.
     *
     * @return string
     */
    public static function generateVehicleNumber()
    {
        $lastVehicle = self::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastVehicle || !$lastVehicle->vehicle_number) {
            return 'VEH-0001';
        }

        // Extract number from last vehicle number (VEH-0001)
        $lastNumber = (int) substr($lastVehicle->vehicle_number, 4);
        $newNumber = $lastNumber + 1;

        return 'VEH-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get vehicle types.
     *
     * @return array
     */
    public static function getVehicleTypes()
    {
        return [
            'truck' => 'Truck',
            'van' => 'Van',
            'forklift' => 'Forklift',
            'reach_truck' => 'Reach Truck',
        ];
    }

    /**
     * Get statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            'available' => 'Available',
            'in_use' => 'In Use',
            'maintenance' => 'Maintenance',
            'inactive' => 'Inactive',
        ];
    }

    /**
     * Get ownerships.
     *
     * @return array
     */
    public static function getOwnerships()
    {
        return [
            'owned' => 'Owned',
            'rented' => 'Rented',
            'leased' => 'Leased',
        ];
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check if vehicle is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    /**
     * Check if vehicle is in use.
     *
     * @return bool
     */
    public function isInUse()
    {
        return $this->status === 'in_use';
    }

    /**
     * Check if vehicle is in maintenance.
     *
     * @return bool
     */
    public function isInMaintenance()
    {
        return $this->status === 'maintenance';
    }

    /**
     * Check if vehicle is inactive.
     *
     * @return bool
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if maintenance is overdue.
     *
     * @return bool
     */
    public function isMaintenanceOverdue()
    {
        return $this->next_maintenance_date 
            && $this->next_maintenance_date < now();
    }

    /**
     * Check if maintenance is due soon (within 7 days).
     *
     * @return bool
     */
    public function isMaintenanceDueSoon()
    {
        return $this->next_maintenance_date 
            && $this->next_maintenance_date >= now()
            && $this->next_maintenance_date <= now()->addDays(7);
    }

    /**
     * Get days until next maintenance.
     *
     * @return int|null
     */
    public function daysUntilMaintenance()
    {
        if (!$this->next_maintenance_date) {
            return null;
        }

        return now()->diffInDays($this->next_maintenance_date, false);
    }

    /**
     * Get days since last maintenance.
     *
     * @return int|null
     */
    public function daysSinceLastMaintenance()
    {
        if (!$this->last_maintenance_date) {
            return null;
        }

        return $this->last_maintenance_date->diffInDays(now());
    }

    /**
     * Mark vehicle as available.
     *
     * @return bool
     */
    public function markAsAvailable()
    {
        return $this->update(['status' => 'available']);
    }

    /**
     * Mark vehicle as in use.
     *
     * @return bool
     */
    public function markAsInUse()
    {
        return $this->update(['status' => 'in_use']);
    }

    /**
     * Mark vehicle as in maintenance.
     *
     * @return bool
     */
    public function markAsMaintenance()
    {
        return $this->update(['status' => 'maintenance']);
    }

    /**
     * Mark vehicle as inactive.
     *
     * @return bool
     */
    public function markAsInactive()
    {
        return $this->update(['status' => 'inactive']);
    }

    /**
     * Update odometer reading.
     *
     * @param int $km
     * @return bool
     */
    public function updateOdometer($km)
    {
        if ($km < $this->odometer_km) {
            return false;
        }

        return $this->update(['odometer_km' => $km]);
    }

    /**
     * Schedule maintenance.
     *
     * @param string $date
     * @return bool
     */
    public function scheduleMaintenance($date)
    {
        return $this->update([
            'next_maintenance_date' => $date,
            'status' => 'maintenance'
        ]);
    }

    /**
     * Complete maintenance.
     *
     * @param string|null $nextMaintenanceDate
     * @return bool
     */
    public function completeMaintenance($nextMaintenanceDate = null)
    {
        return $this->update([
            'last_maintenance_date' => now(),
            'next_maintenance_date' => $nextMaintenanceDate,
            'status' => 'available'
        ]);
    }
}