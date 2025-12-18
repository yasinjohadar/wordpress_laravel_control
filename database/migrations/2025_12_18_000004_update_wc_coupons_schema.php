<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wc_coupons', function (Blueprint $table) {
            if (! Schema::hasColumn('wc_coupons', 'woo_id')) {
                $table->unsignedBigInteger('woo_id')->unique()->after('id');
            }
            if (! Schema::hasColumn('wc_coupons', 'code')) {
                $table->string('code')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'discount_type')) {
                $table->string('discount_type')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'date_expires')) {
                $table->timestamp('date_expires')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'usage_count')) {
                $table->integer('usage_count')->default(0);
            }
            if (! Schema::hasColumn('wc_coupons', 'individual_use')) {
                $table->boolean('individual_use')->default(false);
            }
            if (! Schema::hasColumn('wc_coupons', 'product_ids')) {
                $table->json('product_ids')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'excluded_product_ids')) {
                $table->json('excluded_product_ids')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'usage_limit')) {
                $table->integer('usage_limit')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'usage_limit_per_user')) {
                $table->integer('usage_limit_per_user')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'limit_usage_to_x_items')) {
                $table->integer('limit_usage_to_x_items')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'free_shipping')) {
                $table->boolean('free_shipping')->default(false);
            }
            if (! Schema::hasColumn('wc_coupons', 'product_categories')) {
                $table->json('product_categories')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'excluded_product_categories')) {
                $table->json('excluded_product_categories')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'exclude_sale_items')) {
                $table->boolean('exclude_sale_items')->default(false);
            }
            if (! Schema::hasColumn('wc_coupons', 'minimum_amount')) {
                $table->decimal('minimum_amount', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'maximum_amount')) {
                $table->decimal('maximum_amount', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'email_restrictions')) {
                $table->json('email_restrictions')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'used_by')) {
                $table->json('used_by')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'meta_data')) {
                $table->json('meta_data')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'woo_created_at')) {
                $table->timestamp('woo_created_at')->nullable();
            }
            if (! Schema::hasColumn('wc_coupons', 'woo_updated_at')) {
                $table->timestamp('woo_updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};


