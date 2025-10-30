<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_name',
        'type',
        'api_endpoint',
        'api_key',
        'client_secret',
        'status',
        'last_sync_message',
        'last_synced_at',
    ];

    // Menyembunyikan data sensitif
    protected $hidden = [
        'api_key',
        'client_secret',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}