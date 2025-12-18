<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'wc_products';

    protected $fillable = [
        'woo_id',
        'name',
        'slug',
        'type',
        'status',
        'featured',
        'catalog_visibility',
        'description',
        'short_description',
        'sku',
        'price',
        'regular_price',
        'sale_price',
        'on_sale',
        'purchasable',
        'total_sales',
        'virtual',
        'downloadable',
        'tax_status',
        'tax_class',
        'manage_stock',
        'stock_quantity',
        'stock_status',
        'backorders',
        'weight',
        'dimensions',
        'categories',
        'tags',
        'images',
        'attributes',
        'variations',
        'meta_data',
        'woo_created_at',
        'woo_updated_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'on_sale' => 'boolean',
        'purchasable' => 'boolean',
        'virtual' => 'boolean',
        'downloadable' => 'boolean',
        'manage_stock' => 'boolean',
        'price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'total_sales' => 'integer',
        'stock_quantity' => 'integer',
        'dimensions' => 'array',
        'categories' => 'array',
        'tags' => 'array',
        'images' => 'array',
        'attributes' => 'array',
        'variations' => 'array',
        'meta_data' => 'array',
        'woo_created_at' => 'datetime',
        'woo_updated_at' => 'datetime',
    ];

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function getMainImageAttribute(): ?string
    {
        $images = $this->images ?? [];

        return $images[0]['src'] ?? null;
    }
}


