<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wc_customers', function (Blueprint $table) {
            if (! Schema::hasColumn('wc_customers', 'woo_id')) {
                $table->unsignedBigInteger('woo_id')->unique()->after('id');
            }
            if (! Schema::hasColumn('wc_customers', 'email')) {
                $table->string('email')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'first_name')) {
                $table->string('first_name')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'username')) {
                $table->string('username')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'role')) {
                $table->string('role')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'avatar_url')) {
                $table->string('avatar_url')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'billing_address')) {
                $table->json('billing_address')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'shipping_address')) {
                $table->json('shipping_address')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'is_paying_customer')) {
                $table->boolean('is_paying_customer')->default(false);
            }
            if (! Schema::hasColumn('wc_customers', 'orders_count')) {
                $table->integer('orders_count')->default(0);
            }
            if (! Schema::hasColumn('wc_customers', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0);
            }
            if (! Schema::hasColumn('wc_customers', 'meta_data')) {
                $table->json('meta_data')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'woo_created_at')) {
                $table->timestamp('woo_created_at')->nullable();
            }
            if (! Schema::hasColumn('wc_customers', 'woo_updated_at')) {
                $table->timestamp('woo_updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};


