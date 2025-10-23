<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'brand',
        'model',
        'year',
        'license_plate',
        'capacity_kg',
        'capacity_cbm',
        'fuel_type',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'insurance_expiry',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'capacity_kg' => 'decimal:2',
        'capacity_cbm' => 'decimal:2',
        'year' => 'integer',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'insurance_expiry' => 'date',
        'is_active' => 'boolean',
    ];

    // RELASI
    public function transferOrders()
    {
        return $this->hasMany(TransferOrder::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Generate vehicle number
    public static function generateVehicleNumber()
    {
        $lastVehicle = self::withTrashed()->latest('id')->first();
        $number = $lastVehicle ? intval(substr($lastVehicle->vehicle_number, 4)) + 1 : 1;
        return 'VEH-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}