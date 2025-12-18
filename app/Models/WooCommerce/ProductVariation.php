<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    protected $table = 'wc_product_variations';

    protected $fillable = [
        'woo_id',
        'product_id',
        'sku',
        'price',
        'regular_price',
        'sale_price',
        'stock_status',
        'stock_quantity',
        'manage_stock',
        'attributes',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'attributes' => 'array',
        'image' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}


