<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'metric_date',
        'metric_type',
        'order_fulfillment_rate',
        'picking_accuracy_rate',
        'on_time_shipment_rate',
        'inventory_accuracy_rate',
        'space_utilization_rate',
        'dock_door_utilization_rate',
        'labor_productivity_rate',
        'cost_per_order',
        'average_order_cycle_time',
        'stock_turnover_ratio',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'order_fulfillment_rate' => 'decimal:2',
        'picking_accuracy_rate' => 'decimal:2',
        'on_time_shipment_rate' => 'decimal:2',
        'inventory_accuracy_rate' => 'decimal:2',
        'space_utilization_rate' => 'decimal:2',
        'dock_door_utilization_rate' => 'decimal:2',
        'labor_productivity_rate' => 'decimal:2',
        'cost_per_order' => 'decimal:2',
        'average_order_cycle_time' => 'decimal:2',
        'stock_turnover_ratio' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}