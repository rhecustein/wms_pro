<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'adjustment_number',
        'warehouse_id',
        'adjustment_date',
        'adjustment_type',
        'reason',
        'status',
        'total_items',
        'approved_by',
        'approved_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(StockAdjustmentItem::class);
    }

    //createdBy
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    //updatedBy
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    //approvedBy
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}