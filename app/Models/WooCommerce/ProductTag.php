<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $table = 'wc_product_tags';

    protected $fillable = [
        'woo_id',
        'name',
        'slug',
        'description',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];
}

