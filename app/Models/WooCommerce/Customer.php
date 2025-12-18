<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'wc_customers';

    protected $fillable = [
        'woo_id',
        'email',
        'first_name',
        'last_name',
        'username',
        'role',
        'avatar_url',
        'billing_address',
        'shipping_address',
        'is_paying_customer',
        'orders_count',
        'total_spent',
        'meta_data',
        'woo_created_at',
        'woo_updated_at',
    ];

    protected $casts = [
        'is_paying_customer' => 'boolean',
        'orders_count' => 'integer',
        'total_spent' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'meta_data' => 'array',
        'woo_created_at' => 'datetime',
        'woo_updated_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));

        return $name !== '' ? $name : $this->email;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'woo_id');
    }
}


