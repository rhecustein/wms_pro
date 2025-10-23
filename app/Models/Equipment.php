<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'equipment_number',
        'equipment_type',
        'brand',
        'model',
        'serial_number',
        'warehouse_id',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'operating_hours',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}