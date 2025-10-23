<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'capacity_kg' => 'decimal:2',
        'capacity_cbm' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function transferOrders()
    {
        return $this->hasMany(TransferOrder::class);
    }
}