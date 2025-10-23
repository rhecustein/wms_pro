<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_order_id',
        'product_id',
        'batch_number',
        'serial_number',
        'quantity_returned',
        'quantity_restocked',
        'return_reason',
        'condition',
        'disposition',
        'restocked_to_bin_id',
        'notes',
    ];

    protected $casts = [
        'quantity_returned' => 'decimal:2',
        'quantity_restocked' => 'decimal:2',
    ];

    public function returnOrder()
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function restockedToBin()
    {
        return $this->belongsTo(StorageBin::class, 'restocked_to_bin_id');
    }
}