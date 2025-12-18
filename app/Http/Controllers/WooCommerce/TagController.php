<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\ProductTag;
use App\Services\WooCommerce\TagService;
use App\Services\WooCommerce\Sync\TagSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductTag::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $tags = $query->orderBy('name')->paginate(20);

        return view('admin.woocommerce.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.woocommerce.tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'اسم العلامة مطلوب',
            'name.max' => 'اسم العلامة يجب أن يكون أقل من 255 حرف',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $tagService = new TagService($client);
            
            // إنشاء العلامة في WooCommerce
            $wooTag = $tagService->create($validated);

            // مزامنة العلامة الجديدة إلى قاعدة البيانات المحلية
            $syncService = new TagSyncService($client);
            $syncService->syncTag($wooTag);

            return redirect()->route('woocommerce.tags.index')
                ->with('success', 'تم إنشاء العلامة بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Tag creation error', ['error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء العلامة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $tag = ProductTag::findOrFail($id);

        // جلب المنتجات المرتبطة بهذه العلامة
        $products = collect();
        
        if ($tag->woo_id) {
            // البحث في جميع المنتجات عن تلك التي تحتوي على هذه العلامة
            $allProducts = \App\Models\WooCommerce\Product::all();
            $products = $allProducts->filter(function ($product) use ($tag) {
                $tags = $product->tags ?? [];
                if (is_array($tags)) {
                    foreach ($tags as $productTag) {
                        // يمكن أن يكون $productTag array أو object
                        $tagId = is_array($productTag) ? ($productTag['id'] ?? null) : ($productTag->id ?? null);
                        if ($tagId == $tag->woo_id) {
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
            if ($client->isConfigured() && $tag->woo_id) {
                $freshData = $client->get("products/tags/{$tag->woo_id}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh tag data', [
                'tag_id' => $id,
                'woo_id' => $tag->woo_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.woocommerce.tags.show', compact('tag', 'freshData', 'products'));
    }

    public function edit($id)
    {
        $tag = ProductTag::findOrFail($id);
        
        if (!$tag->woo_id) {
            return redirect()->route('woocommerce.tags.index')
                ->with('error', 'لا يمكن تعديل هذه العلامة لأنها غير مرتبطة بـ WooCommerce.');
        }

        return view('admin.woocommerce.tags.edit', compact('tag'));
    }

    public function update(Request $request, $id)
    {
        $tag = ProductTag::findOrFail($id);
        
        if (!$tag->woo_id) {
            return redirect()->route('woocommerce.tags.index')
                ->with('error', 'لا يمكن تعديل هذه العلامة لأنها غير مرتبطة بـ WooCommerce.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'اسم العلامة مطلوب',
            'name.max' => 'اسم العلامة يجب أن يكون أقل من 255 حرف',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.')->withInput();
            }

            $tagService = new TagService($client);
            
            // تحديث العلامة في WooCommerce
            $wooTag = $tagService->update($tag->woo_id, $validated);

            // مزامنة العلامة المحدثة إلى قاعدة البيانات المحلية
            $syncService = new TagSyncService($client);
            $syncService->syncTag($wooTag);

            return redirect()->route('woocommerce.tags.show', $tag->id)
                ->with('success', 'تم تحديث العلامة بنجاح في WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Tag update error', ['tag_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تحديث العلامة: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $tag = ProductTag::findOrFail($id);
        
        if (!$tag->woo_id) {
            return redirect()->route('woocommerce.tags.index')
                ->with('error', 'لا يمكن حذف هذه العلامة لأنها غير مرتبطة بـ WooCommerce.');
        }

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $tagService = new TagService($client);
            
            // حذف العلامة من WooCommerce
            $tagService->delete($tag->woo_id, true);

            // حذف العلامة من قاعدة البيانات المحلية
            $tag->delete();

            return redirect()->route('woocommerce.tags.index')
                ->with('success', 'تم حذف العلامة بنجاح من WooCommerce.');
        } catch (\Exception $e) {
            Log::error('Tag deletion error', ['tag_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف العلامة: ' . $e->getMessage());
        }
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new TagSyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} علامة بنجاح.");
        } catch (\Exception $e) {
            Log::error('Tag sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}

