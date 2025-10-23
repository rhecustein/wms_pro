<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_adjustment_id',
        'product_id',
        'storage_bin_id',
        'batch_number',
        'serial_number',
        'current_quantity',
        'adjusted_quantity',
        'difference',
        'unit_of_measure',
        'reason',
        'notes',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'adjusted_quantity' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storageBin()
    {
        return $this->belongsTo(StorageBin::class);
    }
}
