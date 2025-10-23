<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'company_name',
        'tax_number',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'contact_person',
        'contact_phone',
        'contact_email',
        'payment_terms',
        'rating',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
    ];

    // RELASI
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function inboundShipments()
    {
        return $this->hasMany(InboundShipment::class);
    }

    public function goodReceivings()
    {
        return $this->hasMany(GoodReceiving::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}