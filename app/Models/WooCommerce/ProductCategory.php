<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'wc_product_categories';

    protected $fillable = [
        'woo_id',
        'name',
        'slug',
        'parent_id',
        'description',
        'image',
        'count',
        'display',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'count' => 'integer',
        'image' => 'array',
    ];

    /**
     * العلاقة مع الفئة الأب
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * العلاقة مع الفئات الفرعية
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}


