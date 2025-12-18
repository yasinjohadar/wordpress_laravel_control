<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wc_products', function (Blueprint $table) {
            if (! Schema::hasColumn('wc_products', 'woo_id')) {
                $table->unsignedBigInteger('woo_id')->unique()->after('id');
            }
            if (! Schema::hasColumn('wc_products', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'slug')) {
                $table->string('slug')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'type')) {
                $table->string('type')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'status')) {
                $table->string('status')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'featured')) {
                $table->boolean('featured')->default(false);
            }
            if (! Schema::hasColumn('wc_products', 'catalog_visibility')) {
                $table->string('catalog_visibility')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'short_description')) {
                $table->text('short_description')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'sku')) {
                $table->string('sku')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'regular_price')) {
                $table->decimal('regular_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'on_sale')) {
                $table->boolean('on_sale')->default(false);
            }
            if (! Schema::hasColumn('wc_products', 'purchasable')) {
                $table->boolean('purchasable')->default(true);
            }
            if (! Schema::hasColumn('wc_products', 'total_sales')) {
                $table->integer('total_sales')->default(0);
            }
            if (! Schema::hasColumn('wc_products', 'virtual')) {
                $table->boolean('virtual')->default(false);
            }
            if (! Schema::hasColumn('wc_products', 'downloadable')) {
                $table->boolean('downloadable')->default(false);
            }
            if (! Schema::hasColumn('wc_products', 'tax_status')) {
                $table->string('tax_status')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'tax_class')) {
                $table->string('tax_class')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'manage_stock')) {
                $table->boolean('manage_stock')->default(false);
            }
            if (! Schema::hasColumn('wc_products', 'stock_quantity')) {
                $table->integer('stock_quantity')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'stock_status')) {
                $table->string('stock_status')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'backorders')) {
                $table->string('backorders')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'weight')) {
                $table->string('weight')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'dimensions')) {
                $table->json('dimensions')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'categories')) {
                $table->json('categories')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'tags')) {
                $table->json('tags')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'images')) {
                $table->json('images')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'attributes')) {
                $table->json('attributes')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'variations')) {
                $table->json('variations')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'meta_data')) {
                $table->json('meta_data')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'woo_created_at')) {
                $table->timestamp('woo_created_at')->nullable();
            }
            if (! Schema::hasColumn('wc_products', 'woo_updated_at')) {
                $table->timestamp('woo_updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        //
    }
};


