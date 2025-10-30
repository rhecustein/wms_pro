<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_code',
        'type',
        'description',
        'is_active',
    ];

    // Hubungan ke tabel products (jika setiap produk memiliki UoM default)
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}