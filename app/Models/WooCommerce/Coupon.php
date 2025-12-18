<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'wc_coupons';

    protected $fillable = [
        'woo_id',
        'code',
        'discount_type',
        'amount',
        'description',
        'date_expires',
        'usage_count',
        'individual_use',
        'product_ids',
        'excluded_product_ids',
        'usage_limit',
        'usage_limit_per_user',
        'limit_usage_to_x_items',
        'free_shipping',
        'product_categories',
        'excluded_product_categories',
        'exclude_sale_items',
        'minimum_amount',
        'maximum_amount',
        'email_restrictions',
        'used_by',
        'meta_data',
        'woo_created_at',
        'woo_updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'usage_count' => 'integer',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'limit_usage_to_x_items' => 'integer',
        'individual_use' => 'boolean',
        'free_shipping' => 'boolean',
        'exclude_sale_items' => 'boolean',
        'product_ids' => 'array',
        'excluded_product_ids' => 'array',
        'product_categories' => 'array',
        'excluded_product_categories' => 'array',
        'email_restrictions' => 'array',
        'used_by' => 'array',
        'meta_data' => 'array',
        'date_expires' => 'datetime',
        'woo_created_at' => 'datetime',
        'woo_updated_at' => 'datetime',
    ];
}


