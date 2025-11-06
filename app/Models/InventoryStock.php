<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'storage_bin_id',
        'product_id',
        'batch_number',
        'serial_number',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'unit_of_measure',
        'manufacturing_date',
        'expiry_date',
        'received_date',
        'pallet_id',
        'status',
        'location_type',
        'customer_id',
        'vendor_id',
        'cost_per_unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',
        'received_date' => 'date',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}