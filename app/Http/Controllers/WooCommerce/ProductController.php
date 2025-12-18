<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Product;
use App\Services\WooCommerce\ProductService;
use App\Services\WooCommerce\Sync\ProductSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $products = $query->orderByDesc('woo_created_at')->paginate(15);

        return view('admin.woocommerce.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.woocommerce.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple,grouped,external,variable',
            'status' => 'required|in:draft,pending,publish',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'tax_status' => 'required|in:taxable,shipping,none',
            'tax_class' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'virtual' => 'boolean',
            'downloadable' => 'boolean',
            'featured' => 'boolean',
            'catalog_visibility' => 'required|in:visible,catalog,search,hidden',
        ], [
            'name.required' => 'اسم المنتج مطلوب',
            'type.required' => 'نوع المنتج مطلوب',
            'status.required' => 'حالة المنتج مطلوبة',
            'regular_price.required' => 'السعر العادي مطلوب',
            'regular_price.numeric' => 'السعر يجب أن يكون رقماً',
            'regular_price.min' => 'السعر يجب أن يكون أكبر من أو يساوي صفر',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $productService = new ProductService($client);
            
            // تحويل البيانات إلى تنسيق مناسب
            $productData = $validated;
            if (isset($validated['length']) || isset($validated['width']) || isset($validated['height'])) {
                $productData['dimensions'] = [
                    'length' => $validated['length'] ?? null,
                    'width' => $validated['width'] ?? null,
                    'height' => $validated['height'] ?? null,
                ];
            }

            // إنشاء المنتج في WooCommerce
            $wooProduct = $productService->create($productData);

            // مزامنة المنتج الجديد إلى قاعدة البيانات المحلية
            $syncService = new ProductSyncService($client);
            $syncService->syncProduct($wooProduct);

            return redirect()->route('woocommerce.products.index')
                ->with('success', 'تم إنشاء المنتج بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Product creation error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // محاولة جلب أحدث بيانات من WooCommerce إذا كان متاحاً
        $freshData = null;
        try {
            $client = new WooCommerceClient();
            if ($client->isConfigured() && $product->woo_id) {
                $freshData = $client->get("products/{$product->woo_id}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh product data', [
                'product_id' => $id,
                'woo_id' => $product->woo_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.woocommerce.products.show', compact('product', 'freshData'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->woo_id) {
            return redirect()->route('woocommerce.products.index')
                ->with('error', 'لا يمكن تعديل هذا المنتج لأنه غير مرتبط بـ WooCommerce.');
        }

        return view('admin.woocommerce.products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->woo_id) {
            return redirect()->route('woocommerce.products.index')
                ->with('error', 'لا يمكن تعديل هذا المنتج لأنه غير مرتبط بـ WooCommerce.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:simple,grouped,external,variable',
            'status' => 'required|in:draft,pending,publish',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'nullable|string|max:100',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'stock_status' => 'required|in:instock,outofstock,onbackorder',
            'tax_status' => 'required|in:taxable,shipping,none',
            'tax_class' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'virtual' => 'boolean',
            'downloadable' => 'boolean',
            'featured' => 'boolean',
            'catalog_visibility' => 'required|in:visible,catalog,search,hidden',
        ], [
            'name.required' => 'اسم المنتج مطلوب',
            'type.required' => 'نوع المنتج مطلوب',
            'status.required' => 'حالة المنتج مطلوبة',
            'regular_price.required' => 'السعر العادي مطلوب',
            'regular_price.numeric' => 'السعر يجب أن يكون رقماً',
            'regular_price.min' => 'السعر يجب أن يكون أكبر من أو يساوي صفر',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $productService = new ProductService($client);
            
            // تحويل البيانات إلى تنسيق مناسب
            $productData = $validated;
            if (isset($validated['length']) || isset($validated['width']) || isset($validated['height'])) {
                $productData['dimensions'] = [
                    'length' => $validated['length'] ?? null,
                    'width' => $validated['width'] ?? null,
                    'height' => $validated['height'] ?? null,
                ];
            }

            // تحديث المنتج في WooCommerce
            $wooProduct = $productService->update($product->woo_id, $productData);

            // مزامنة المنتج المحدث إلى قاعدة البيانات المحلية
            $syncService = new ProductSyncService($client);
            $syncService->syncProduct($wooProduct);

            return redirect()->route('woocommerce.products.show', $product->id)
                ->with('success', 'تم تحديث المنتج بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Product update error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if (!$product->woo_id) {
            return redirect()->route('woocommerce.products.index')
                ->with('error', 'لا يمكن حذف هذا المنتج لأنه غير مرتبط بـ WooCommerce.');
        }

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $productService = new ProductService($client);
            
            // حذف المنتج من WooCommerce
            $productService->delete($product->woo_id, true);

            // حذف المنتج من قاعدة البيانات المحلية
            $product->delete();

            return redirect()->route('woocommerce.products.index')
                ->with('success', 'تم حذف المنتج بنجاح من WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Product deletion error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new ProductSyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} منتج بنجاح.");
        } catch (\Exception $e) {
            Log::error('Product sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}


