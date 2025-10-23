<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'storage_bin_id',
        'batch_number',
        'serial_number',
        'system_quantity',
        'physical_quantity',
        'variance',
        'variance_value',
        'status',
        'counted_by',
        'counted_at',
        'notes',
    ];

    protected $casts = [
        'system_quantity' => 'decimal:2',
        'physical_quantity' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_value' => 'decimal:2',
        'counted_at' => 'datetime',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }

    public function countedByUser()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }
}