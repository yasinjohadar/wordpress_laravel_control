<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wc_product_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('wc_product_categories', 'woo_id')) {
                $table->unsignedBigInteger('woo_id')->unique()->nullable();
            }
            if (!Schema::hasColumn('wc_product_categories', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('wc_product_categories', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
            if (!Schema::hasColumn('wc_product_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
            }
            if (!Schema::hasColumn('wc_product_categories', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('wc_product_categories', 'image')) {
                $table->json('image')->nullable();
            }
            if (!Schema::hasColumn('wc_product_categories', 'count')) {
                $table->integer('count')->default(0);
            }
            if (!Schema::hasColumn('wc_product_categories', 'display')) {
                $table->string('display')->default('default');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wc_product_categories', function (Blueprint $table) {
            //
        });
    }
};
