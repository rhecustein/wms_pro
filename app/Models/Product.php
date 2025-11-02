<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'description',
        'category_id',
        'brand',
        'unit_id',
        'supplier_id',
        'purchase_price',
        'selling_price',
        'minimum_selling_price',
        'minimum_stock',
        'maximum_stock',
        'reorder_level',
        'current_stock',
        'weight',
        'length',
        'width',
        'height',
        'is_taxable',
        'tax_rate',
        'type',
        'is_active',
        'is_serialized',
        'is_batch_tracked',
        'image',
        'images',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'reorder_level' => 'integer',
        'current_stock' => 'integer',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_taxable' => 'boolean',
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
        'is_serialized' => 'boolean',
        'is_batch_tracked' => 'boolean',
        'images' => 'array',
    ];

    protected $appends = [
        'status_badge',
        'stock_status',
        'stock_status_color',
        'profit_margin',
        'profit_amount',
        'image_url',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inboundItems(): HasMany
    {
        return $this->hasMany(InboundItem::class);
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

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'Out of Stock';
        } elseif ($this->current_stock <= $this->reorder_level) {
            return 'Low Stock';
        } elseif ($this->current_stock >= $this->maximum_stock) {
            return 'Overstock';
        }
        return 'In Stock';
    }

    public function getStockStatusColorAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'text-red-600';
        } elseif ($this->current_stock <= $this->reorder_level) {
            return 'text-yellow-600';
        } elseif ($this->current_stock >= $this->maximum_stock) {
            return 'text-blue-600';
        }
        return 'text-green-600';
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->purchase_price > 0) {
            return (($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100;
        }
        return 0;
    }

    public function getProfitAmountAttribute(): float
    {
        return $this->selling_price - $this->purchase_price;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return asset('images/no-image.png');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_level')
                     ->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeOverstock($query)
    {
        return $query->whereColumn('current_stock', '>=', 'maximum_stock');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%");
        });
    }

    // Methods
    public function updateStock(float $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->current_stock += $quantity;
        } elseif ($type === 'subtract') {
            $this->current_stock -= $quantity;
        } elseif ($type === 'set') {
            $this->current_stock = $quantity;
        }
        
        $this->save();
    }

    public function needsReorder(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function isOverstock(): bool
    {
        return $this->current_stock >= $this->maximum_stock;
    }

    // Static Methods
    public static function generateSku(): string
    {
        $lastProduct = self::withTrashed()->latest('id')->first();
        $number = $lastProduct ? intval(substr($lastProduct->sku, 4)) + 1 : 1;
        return 'PRD-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSku();
            }
            if (auth()->check()) {
                $product->created_by = auth()->id();
            }
        });

        static::updating(function ($product) {
            if (auth()->check()) {
                $product->updated_by = auth()->id();
            }
        });

        static::deleting(function ($product) {
            // Delete image if exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
            // Delete multiple images
            if ($product->images && is_array($product->images)) {
                foreach ($product->images as $image) {
                    if (Storage::exists($image)) {
                        Storage::delete($image);
                    }
                }
            }
        });
    }
}