<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // SCOPES
    // ==========================================
    
    /**
     * Scope untuk filter user berdasarkan role slug
     */
    public function scopeWithRoles($query, $roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $query->whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('slug', $roles);
        });
    }

    /**
     * Scope untuk filter user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    /**
     * User memiliki banyak roles (Many-to-Many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Warehouse yang di-manage oleh user ini
     */
    public function managedWarehouses()
    {
        return $this->hasMany(Warehouse::class, 'manager_id');
    }

    /**
     * Records yang dibuat oleh user ini
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Records yang diupdate oleh user ini
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Transfer orders sebagai driver
     */
    public function driverTransferOrders()
    {
        return $this->hasMany(TransferOrder::class, 'driver_id');
    }

    /**
     * Delivery orders sebagai driver
     */
    public function driverDeliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'driver_id');
    }

    /**
     * Transfer orders yang diapprove
     */
    public function approvedTransferOrders()
    {
        return $this->hasMany(TransferOrder::class, 'approved_by');
    }

    /**
     * Picking orders yang diassign
     */
    public function assignedPickingOrders()
    {
        return $this->hasMany(PickingOrder::class, 'assigned_to');
    }

    /**
     * Packing orders yang diassign
     */
    public function assignedPackingOrders()
    {
        return $this->hasMany(PackingOrder::class, 'assigned_to');
    }

    /**
     * Good receivings yang diterima
     */
    public function receivedGoodReceivings()
    {
        return $this->hasMany(GoodReceiving::class, 'received_by');
    }

    /**
     * Return orders yang diinspeksi
     */
    public function inspectedReturnOrders()
    {
        return $this->hasMany(ReturnOrder::class, 'inspected_by');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================
    
    /**
     * Check apakah user memiliki role tertentu
     */
    public function hasRole($roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check apakah user memiliki salah satu dari roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    /**
     * Get semua role slugs dari user
     */
    public function getRoleNames()
    {
        return $this->roles->pluck('slug')->toArray();
    }
}