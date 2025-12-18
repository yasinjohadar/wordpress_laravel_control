<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'wc_order_items';

    protected $fillable = [
        'woo_id',
        'order_id',
        'product_id',
        'variation_id',
        'name',
        'quantity',
        'subtotal',
        'subtotal_tax',
        'total',
        'total_tax',
        'sku',
        'price',
        'meta_data',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'subtotal_tax' => 'decimal:2',
        'total' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'price' => 'decimal:2',
        'meta_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}


