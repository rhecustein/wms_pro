<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'company_name',
        'email',
        'phone',
        'mobile',
        'fax',
        'website',
        'tax_number',
        'tax_name',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'contact_person',
        'contact_email',
        'contact_phone',
        'payment_term_days',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'rating',
        'type',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'payment_term_days' => 'integer',
    ];

    protected $appends = [
        'status_badge',
        'rating_badge',
        'full_address',
    ];

    // Relations
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function inbounds(): HasMany
    {
        return $this->hasMany(Inbound::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return '<span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                <i class="fas fa-check-circle mr-1"></i>Active
            </span>';
        }
        return '<span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
            <i class="fas fa-times-circle mr-1"></i>Inactive
        </span>';
    }

    public function getRatingBadgeAttribute(): string
    {
        $colors = [
            'A' => 'bg-green-100 text-green-800',
            'B' => 'bg-blue-100 text-blue-800',
            'C' => 'bg-yellow-100 text-yellow-800',
            'D' => 'bg-red-100 text-red-800',
        ];

        $color = $colors[$this->rating] ?? 'bg-gray-100 text-gray-800';

        return '<span class="px-2 py-1 text-xs font-semibold rounded ' . $color . '">
            Rating ' . $this->rating . '
        </span>';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Static Methods
    public static function generateCode(): string
    {
        $lastSupplier = self::withTrashed()->latest('id')->first();
        $number = $lastSupplier ? intval(substr($lastSupplier->code, 4)) + 1 : 1;
        return 'SUP-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->code)) {
                $supplier->code = self::generateCode();
            }
        });
    }
}