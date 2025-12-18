<?php

namespace App\Models\WooCommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'wc_orders';

    protected $fillable = [
        'woo_id',
        'order_number',
        'status',
        'currency',
        'currency_symbol',
        'total',
        'subtotal',
        'total_tax',
        'shipping_total',
        'discount_total',
        'discount_tax',
        'payment_method',
        'payment_method_title',
        'transaction_id',
        'customer_id',
        'customer_ip_address',
        'customer_user_agent',
        'customer_note',
        'billing_address',
        'shipping_address',
        'line_items',
        'shipping_lines',
        'fee_lines',
        'coupon_lines',
        'refunds',
        'meta_data',
        'date_paid',
        'date_completed',
        'woo_created_at',
        'woo_updated_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'discount_tax' => 'decimal:2',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'line_items' => 'array',
        'shipping_lines' => 'array',
        'fee_lines' => 'array',
        'coupon_lines' => 'array',
        'refunds' => 'array',
        'meta_data' => 'array',
        'date_paid' => 'datetime',
        'date_completed' => 'datetime',
        'woo_created_at' => 'datetime',
        'woo_updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        $symbol = $this->currency_symbol ?: 'ر.س';

        return number_format($this->total, 2) . ' ' . $symbol;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'woo_id');
    }
}


