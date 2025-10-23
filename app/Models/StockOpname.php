<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opname_number',
        'warehouse_id',
        'storage_area_id',
        'opname_date',
        'opname_type',
        'status',
        'scheduled_by',
        'completed_by',
        'started_at',
        'completed_at',
        'total_items_planned',
        'total_items_counted',
        'variance_count',
        'accuracy_percentage',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'accuracy_percentage' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function storageArea()
    {
        return $this->belongsTo(StorageArea::class);
    }

    public function scheduledByUser()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    //scheduledBy
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //updatedBy
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function scheduledBy()
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }
}