<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['status_badge', 'full_path'];

    // Relations
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
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

    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Methods
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }
        
        return $descendants;
    }

    public function getAncestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors->reverse();
    }

    // Static Methods
    public static function generateCode(): string
    {
        $lastCategory = self::withTrashed()->latest('id')->first();
        $number = $lastCategory ? intval(substr($lastCategory->code, 4)) + 1 : 1;
        return 'CAT-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public static function getTree($parentId = null): \Illuminate\Support\Collection
    {
        return self::where('parent_id', $parentId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                $category->children = self::getTree($category->id);
                return $category;
            });
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = self::generateCode();
            }
        });

        static::deleting(function ($category) {
            // Update products to null when category is deleted
            $category->products()->update(['category_id' => null]);
            
            // Move children to parent's level
            $category->children()->update(['parent_id' => $category->parent_id]);
        });
    }
}