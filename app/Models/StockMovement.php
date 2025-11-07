<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'warehouse_id',
        'product_id',
        'from_bin_id',
        'to_bin_id',
        'batch_number',
        'serial_number',
        'quantity',
        'unit_of_measure',
        'movement_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'movement_date',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'movement_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromBin()
    {
        return $this->belongsTo(StorageBin::class, 'from_bin_id');
    }

    public function toBin()
    {
        return $this->belongsTo(StorageBin::class, 'to_bin_id');
    }

    public function performedByUser()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    //performedBy
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}