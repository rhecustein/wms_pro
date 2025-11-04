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

    public function inventoryStocks(): HasMany
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Active
            </span>';
        }
        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Inactive
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
            return 'red';
        } elseif ($this->current_stock <= $this->reorder_level) {
            return 'yellow';
        } elseif ($this->current_stock >= $this->maximum_stock) {
            return 'blue';
        }
        return 'green';
    }

    public function getStockStatusBadgeAttribute(): string
    {
        $status = $this->stock_status;
        $color = $this->stock_status_color;
        
        $badgeClasses = [
            'red' => 'bg-red-100 text-red-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
        ];
        
        $iconClasses = [
            'red' => 'bg-red-500',
            'yellow' => 'bg-yellow-500',
            'blue' => 'bg-blue-500',
            'green' => 'bg-green-500',
        ];
        
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium %s">
                <span class="w-1.5 h-1.5 %s rounded-full mr-1.5"></span>%s
            </span>',
            $badgeClasses[$color],
            $iconClasses[$color],
            $status
        );
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

    public function getTotalWeightAttribute(): float
    {
        return ($this->weight ?? 0) * $this->current_stock;
    }

    public function getTotalVolumeAttribute(): float
    {
        if (!$this->length || !$this->width || !$this->height) {
            return 0;
        }
        // Calculate volume in m³ from dimensions in cm
        $volumeInCubicCm = $this->length * $this->width * $this->height;
        $volumeInCubicM = $volumeInCubicCm / 1000000; // Convert cm³ to m³
        return $volumeInCubicM * $this->current_stock;
    }

    // Helper Accessors for Display
    public function getFormattedWeightAttribute(): string
    {
        if (!$this->weight) {
            return '-';
        }
        return number_format($this->weight, 2) . ' kg';
    }

    public function getFormattedVolumeAttribute(): string
    {
        if (!$this->length || !$this->width || !$this->height) {
            return '-';
        }
        $volumeInCubicCm = $this->length * $this->width * $this->height;
        $volumeInCubicM = $volumeInCubicCm / 1000000;
        return number_format($volumeInCubicM, 6) . ' m³';
    }

    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->length || !$this->width || !$this->height) {
            return '-';
        }
        return sprintf(
            '%s × %s × %s cm',
            number_format($this->length, 2),
            number_format($this->width, 2),
            number_format($this->height, 2)
        );
    }

    public function getTypeDisplayAttribute(): string
    {
        $types = [
            'raw_material' => 'Raw Material',
            'finished_goods' => 'Finished Goods',
            'spare_parts' => 'Spare Parts',
            'consumable' => 'Consumable',
        ];
        
        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getTypeBadgeAttribute(): string
    {
        $badges = [
            'raw_material' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Raw Material</span>',
            'finished_goods' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Finished Goods</span>',
            'spare_parts' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Spare Parts</span>',
            'consumable' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">Consumable</span>',
        ];
        
        return $badges[$this->type] ?? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' . ucfirst($this->type) . '</span>';
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

    public function scopeSerialized($query)
    {
        return $query->where('is_serialized', true);
    }

    public function scopeBatchTracked($query)
    {
        return $query->where('is_batch_tracked', true);
    }

    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Methods
    public function updateStock(float $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->current_stock += $quantity;
        } elseif ($type === 'subtract') {
            $this->current_stock = max(0, $this->current_stock - $quantity);
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

    public function isLowStock(): bool
    {
        return $this->current_stock > 0 && $this->current_stock <= $this->reorder_level;
    }

    public function hasStock(): bool
    {
        return $this->current_stock > 0;
    }

    public function canSell(float $quantity): bool
    {
        return $this->current_stock >= $quantity;
    }

    public function calculateTotalValue(): float
    {
        return $this->current_stock * $this->selling_price;
    }

    public function calculateTotalCost(): float
    {
        return $this->current_stock * $this->purchase_price;
    }

    public function calculateTotalProfit(): float
    {
        return $this->current_stock * $this->profit_amount;
    }

    public function getStockPercentage(): float
    {
        if ($this->maximum_stock <= 0) {
            return 0;
        }
        return ($this->current_stock / $this->maximum_stock) * 100;
    }

    // Static Methods
    public static function generateSku(string $prefix = 'PRD'): string
    {
        $lastProduct = self::withTrashed()
            ->where('sku', 'like', $prefix . '-%')
            ->latest('id')
            ->first();
        
        if ($lastProduct) {
            $lastNumber = intval(substr($lastProduct->sku, strlen($prefix) + 1));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }
        
        return $prefix . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public static function getProductTypes(): array
    {
        return [
            'raw_material' => 'Raw Material',
            'finished_goods' => 'Finished Goods',
            'spare_parts' => 'Spare Parts',
            'consumable' => 'Consumable',
        ];
    }

    public static function getStockStatistics(): array
    {
        return [
            'total_products' => self::count(),
            'active_products' => self::active()->count(),
            'inactive_products' => self::inactive()->count(),
            'low_stock' => self::lowStock()->count(),
            'out_of_stock' => self::outOfStock()->count(),
            'overstock' => self::overstock()->count(),
            'total_stock_value' => self::active()->get()->sum(fn($p) => $p->calculateTotalValue()),
            'total_stock_cost' => self::active()->get()->sum(fn($p) => $p->calculateTotalCost()),
        ];
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