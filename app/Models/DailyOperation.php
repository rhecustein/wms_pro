<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'operation_date',
        'total_inbound_orders',
        'total_outbound_orders',
        'total_items_received',
        'total_items_picked',
        'total_items_shipped',
        'total_pallets_moved',
        'total_replenishments',
        'total_transfers',
        'total_adjustments',
        'average_picking_time_minutes',
        'average_putaway_time_minutes',
        'total_staff_hours',
    ];

    protected $casts = [
        'operation_date' => 'date',
        'average_picking_time_minutes' => 'decimal:2',
        'average_putaway_time_minutes' => 'decimal:2',
        'total_staff_hours' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}