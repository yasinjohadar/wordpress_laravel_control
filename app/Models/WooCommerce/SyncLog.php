<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $table = 'wc_sync_logs';

    protected $fillable = [
        'type',
        'direction',
        'status',
        'total_items',
        'synced_items',
        'failed_items',
        'errors',
        'started_at',
        'completed_at',
        'duration_seconds',
    ];

    protected $casts = [
        'total_items' => 'integer',
        'synced_items' => 'integer',
        'failed_items' => 'integer',
        'errors' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];
}


