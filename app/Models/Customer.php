<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'country',
        'tax_id',
        'customer_type',
        'credit_limit',
        'payment_terms_days',
        'contact_person',
        'contact_phone',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_limit' => 'decimal:2',
    ];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }
}