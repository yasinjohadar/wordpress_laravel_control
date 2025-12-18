<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WooCommerce\DashboardController as WooDashboardController;
use App\Http\Controllers\WooCommerce\ProductController as WooProductController;
use App\Http\Controllers\WooCommerce\OrderController as WooOrderController;
use App\Http\Controllers\WooCommerce\CustomerController as WooCustomerController;
use App\Http\Controllers\WooCommerce\CouponController as WooCouponController;
use App\Http\Controllers\WooCommerce\ReportController as WooReportController;
use App\Http\Controllers\WooCommerce\CategoryController as WooCategoryController;
use App\Http\Controllers\WooCommerce\TagController as WooTagController;

Route::get('/', function () {
    return view('admin.dashboard');
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'check.user.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::put('users/{user}/change-password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // WooCommerce routes (لوحة المتجر + القوائم الأساسية)
    Route::prefix('woocommerce')->name('woocommerce.')->group(function () {
        Route::get('/', [WooDashboardController::class, 'index'])->name('dashboard');
        Route::post('/sync', [WooDashboardController::class, 'sync'])->name('sync');
        Route::get('/test-connection', [WooDashboardController::class, 'testConnection'])->name('test-connection');

        Route::get('/products', [WooProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [WooProductController::class, 'create'])->name('products.create');
        Route::post('/products', [WooProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [WooProductController::class, 'show'])->name('products.show');
        Route::get('/products/{id}/edit', [WooProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [WooProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [WooProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/sync', [WooProductController::class, 'sync'])->name('products.sync');

        Route::get('/orders', [WooOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [WooOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/sync', [WooOrderController::class, 'sync'])->name('orders.sync');
        Route::put('/orders/{id}/status', [WooOrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{id}/notes', [WooOrderController::class, 'addNote'])->name('orders.add-note');
        Route::get('/orders/{id}/invoice', [WooOrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/export', [WooOrderController::class, 'export'])->name('orders.export');

        Route::get('/customers', [WooCustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/create', [WooCustomerController::class, 'create'])->name('customers.create');
        Route::post('/customers', [WooCustomerController::class, 'store'])->name('customers.store');
        Route::get('/customers/{id}', [WooCustomerController::class, 'show'])->name('customers.show');
        Route::get('/customers/{id}/edit', [WooCustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{id}', [WooCustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{id}', [WooCustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/customers/sync', [WooCustomerController::class, 'sync'])->name('customers.sync');

        Route::get('/coupons', [WooCouponController::class, 'index'])->name('coupons.index');
        Route::post('/coupons/sync', [WooCouponController::class, 'sync'])->name('coupons.sync');

        // Reports routes
        Route::get('/reports', [WooReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [WooReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/products', [WooReportController::class, 'products'])->name('reports.products');
        Route::get('/reports/customers', [WooReportController::class, 'customers'])->name('reports.customers');
        Route::get('/reports/chart-data', [WooReportController::class, 'getChartData'])->name('reports.chart-data');

        // Categories routes
        Route::get('/categories', [WooCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [WooCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [WooCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}', [WooCategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{id}/edit', [WooCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{id}', [WooCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [WooCategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/sync', [WooCategoryController::class, 'sync'])->name('categories.sync');

        // Tags routes
        Route::get('/tags', [WooTagController::class, 'index'])->name('tags.index');
        Route::get('/tags/create', [WooTagController::class, 'create'])->name('tags.create');
        Route::post('/tags', [WooTagController::class, 'store'])->name('tags.store');
        Route::get('/tags/{id}', [WooTagController::class, 'show'])->name('tags.show');
        Route::get('/tags/{id}/edit', [WooTagController::class, 'edit'])->name('tags.edit');
        Route::put('/tags/{id}', [WooTagController::class, 'update'])->name('tags.update');
        Route::delete('/tags/{id}', [WooTagController::class, 'destroy'])->name('tags.destroy');
        Route::post('/tags/sync', [WooTagController::class, 'sync'])->name('tags.sync');
    });
});

// مسار toggle-status بدون middleware check.user.active
Route::middleware(['auth'])->group(function () {
    Route::post('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

// مسار بديل للتجربة
Route::post('toggle-user-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggle-status-alt');

// Webhook route (بدون middleware auth لأن WooCommerce سيرسل الطلبات من الخارج)
Route::post('/woocommerce/webhook', [\App\Http\Controllers\WooCommerce\WebhookController::class, 'handle'])
    ->name('woocommerce.webhook');

require __DIR__.'/auth.php';
