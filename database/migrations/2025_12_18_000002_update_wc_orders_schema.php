<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wc_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('wc_orders', 'woo_id')) {
                $table->unsignedBigInteger('woo_id')->unique()->after('id');
            }
            if (! Schema::hasColumn('wc_orders', 'order_number')) {
                $table->string('order_number')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'status')) {
                $table->string('status')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'currency')) {
                $table->string('currency')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'currency_symbol')) {
                $table->string('currency_symbol')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'total')) {
                $table->decimal('total', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'total_tax')) {
                $table->decimal('total_tax', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'shipping_total')) {
                $table->decimal('shipping_total', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'discount_total')) {
                $table->decimal('discount_total', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'discount_tax')) {
                $table->decimal('discount_tax', 12, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'payment_method_title')) {
                $table->string('payment_method_title')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'transaction_id')) {
                $table->string('transaction_id')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'customer_ip_address')) {
                $table->string('customer_ip_address')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'customer_user_agent')) {
                $table->text('customer_user_agent')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'customer_note')) {
                $table->text('customer_note')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'billing_address')) {
                $table->json('billing_address')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'line_items')) {
                $table->json('line_items')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'shipping_lines')) {
                $table->json('shipping_lines')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'fee_lines')) {
                $table->json('fee_lines')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'coupon_lines')) {
                $table->json('coupon_lines')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'refunds')) {
                $table->json('refunds')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'meta_data')) {
                $table->json('meta_data')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'date_paid')) {
                $table->timestamp('date_paid')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'date_completed')) {
                $table->timestamp('date_completed')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'woo_created_at')) {
                $table->timestamp('woo_created_at')->nullable();
            }
            if (! Schema::hasColumn('wc_orders', 'woo_updated_at')) {
                $table->timestamp('woo_updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};


