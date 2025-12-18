<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\ProductCategory;
use App\Services\WooCommerce\CategoryService;
use App\Services\WooCommerce\Sync\CategorySyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductCategory::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($parentId = $request->get('parent_id')) {
            if ($parentId === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        $categories = $query->with('parent', 'children')->orderBy('name')->paginate(20);

        // جلب الفئات الرئيسية للفلترة
        $parentCategories = ProductCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.woocommerce.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = ProductCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.woocommerce.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:wc_product_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
            'display' => 'nullable|in:default,products,subcategories,both',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.max' => 'اسم الفئة يجب أن يكون أقل من 255 حرف',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $categoryService = new CategoryService($client);
            
            // إذا كان هناك parent_id محلي، نحتاج إلى woo_id الخاص به
            if (isset($validated['parent_id'])) {
                $parentCategory = ProductCategory::find($validated['parent_id']);
                if ($parentCategory && $parentCategory->woo_id) {
                    $validated['parent_id'] = $parentCategory->woo_id;
                } else {
                    unset($validated['parent_id']);
                }
            }

            // إنشاء الفئة في WooCommerce
            $wooCategory = $categoryService->create($validated);

            // مزامنة الفئة الجديدة إلى قاعدة البيانات المحلية
            $syncService = new CategorySyncService($client);
            $syncService->syncCategory($wooCategory);

            return redirect()->route('woocommerce.categories.index')
                ->with('success', 'تم إنشاء الفئة بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Category creation error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $category = ProductCategory::with('parent', 'children')->findOrFail($id);

        // جلب المنتجات المرتبطة بهذه الفئة
        $products = collect();
        
        if ($category->woo_id) {
            // البحث في جميع المنتجات عن تلك التي تحتوي على هذه الفئة
            $allProducts = \App\Models\WooCommerce\Product::all();
            $products = $allProducts->filter(function ($product) use ($category) {
                $categories = $product->categories ?? [];
                if (is_array($categories)) {
                    foreach ($categories as $cat) {
                        // يمكن أن يكون $cat array أو object
                        $catId = is_array($cat) ? ($cat['id'] ?? null) : ($cat->id ?? null);
                        if ($catId == $category->woo_id) {
                            return true;
                        }
                    }
                }
                return false;
            })->values(); // values() لإعادة فهرسة المصفوفة
        }

        // محاولة جلب أحدث بيانات من WooCommerce
        $freshData = null;
        try {
            $client = new WooCommerceClient();
            if ($client->isConfigured() && $category->woo_id) {
                $freshData = $client->get("products/categories/{$category->woo_id}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh category data', [
                'category_id' => $id,
                'woo_id' => $category->woo_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.woocommerce.categories.show', compact('category', 'freshData', 'products'));
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        
        if (!$category->woo_id) {
            return redirect()->route('woocommerce.categories.index')
                ->with('error', 'لا يمكن تعديل هذه الفئة لأنها غير مرتبطة بـ WooCommerce.');
        }

        $parentCategories = ProductCategory::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        return view('admin.woocommerce.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);
        
        if (!$category->woo_id) {
            return redirect()->route('woocommerce.categories.index')
                ->with('error', 'لا يمكن تعديل هذه الفئة لأنها غير مرتبطة بـ WooCommerce.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:wc_product_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|url',
            'display' => 'nullable|in:default,products,subcategories,both',
        ], [
            'name.required' => 'اسم الفئة مطلوب',
            'name.max' => 'اسم الفئة يجب أن يكون أقل من 255 حرف',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $categoryService = new CategoryService($client);
            
            // إذا كان هناك parent_id محلي، نحتاج إلى woo_id الخاص به
            if (isset($validated['parent_id'])) {
                $parentCategory = ProductCategory::find($validated['parent_id']);
                if ($parentCategory && $parentCategory->woo_id) {
                    $validated['parent_id'] = $parentCategory->woo_id;
                } else {
                    unset($validated['parent_id']);
                }
            }

            // تحديث الفئة في WooCommerce
            $wooCategory = $categoryService->update($category->woo_id, $validated);

            // مزامنة الفئة المحدثة إلى قاعدة البيانات المحلية
            $syncService = new CategorySyncService($client);
            $syncService->syncCategory($wooCategory);

            return redirect()->route('woocommerce.categories.show', $category->id)
                ->with('success', 'تم تحديث الفئة بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Category update error', ['category_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث الفئة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);
        
        if (!$category->woo_id) {
            return redirect()->route('woocommerce.categories.index')
                ->with('error', 'لا يمكن حذف هذه الفئة لأنها غير مرتبطة بـ WooCommerce.');
        }

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $categoryService = new CategoryService($client);
            
            // حذف الفئة من WooCommerce
            $categoryService->delete($category->woo_id, true);

            // حذف الفئة من قاعدة البيانات المحلية
            $category->delete();

            return redirect()->route('woocommerce.categories.index')
                ->with('success', 'تم حذف الفئة بنجاح من WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Category deletion error', ['category_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الفئة: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new CategorySyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} فئة بنجاح.");
        } catch (\Exception $e) {
            Log::error('Category sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}

