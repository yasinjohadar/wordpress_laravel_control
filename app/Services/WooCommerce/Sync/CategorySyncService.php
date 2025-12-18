<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\ProductCategory;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\Log;

class CategorySyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع الفئات من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'categories',
            'direction' => 'from_woocommerce',
            'status' => 'running',
            'total_items' => 0,
            'synced_items' => 0,
            'failed_items' => 0,
            'errors' => [],
            'started_at' => now(),
        ]);

        $synced = 0;
        $failed = 0;
        $errors = [];

        try {
            $categories = $this->client->getAllPaginated('products/categories', [], 50);

            $log->update(['total_items' => count($categories)]);

            foreach ($categories as $categoryData) {
                try {
                    $categoryArray = $this->toArray($categoryData);
                    $this->syncCategory($categoryArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($categories));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $categoryArray = $this->toArray($categoryData);
                    $errors[] = [
                        'woo_id' => $categoryArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Category sync failed', [
                        'woo_id' => $categoryArray['id'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $log->update([
                'status' => 'completed',
                'synced_items' => $synced,
                'failed_items' => $failed,
                'errors' => $errors,
                'completed_at' => now(),
                'duration_seconds' => now()->diffInSeconds($log->started_at),
            ]);

            return [
                'success' => true,
                'synced' => $synced,
                'failed' => $failed,
                'total' => count($categories),
            ];
        } catch (Exception $e) {
            $log->update([
                'status' => 'failed',
                'errors' => array_merge($errors, [['error' => $e->getMessage()]]),
                'completed_at' => now(),
                'duration_seconds' => now()->diffInSeconds($log->started_at),
            ]);

            throw $e;
        }
    }

    /**
     * مزامنة فئة واحدة
     */
    public function syncCategory(array $categoryData): ProductCategory
    {
        $data = $this->transformCategoryData($categoryData);

        return ProductCategory::updateOrCreate(
            ['woo_id' => $categoryData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات الفئة من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformCategoryData(array $categoryData): array
    {
        // البحث عن parent_id محلي إذا كان هناك parent في WooCommerce
        $parentId = null;
        if (isset($categoryData['parent']) && $categoryData['parent'] > 0) {
            $parentCategory = ProductCategory::where('woo_id', $categoryData['parent'])->first();
            if ($parentCategory) {
                $parentId = $parentCategory->id;
            }
        }

        return [
            'woo_id' => $categoryData['id'],
            'name' => $categoryData['name'] ?? '',
            'slug' => $categoryData['slug'] ?? '',
            'parent_id' => $parentId,
            'description' => $categoryData['description'] ?? null,
            'image' => isset($categoryData['image']) ? $categoryData['image'] : null,
            'count' => $categoryData['count'] ?? 0,
            'display' => $categoryData['display'] ?? 'default',
        ];
    }

    protected function toArray(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            $json = json_encode($data);
            if ($json === false) {
                return [];
            }
            $array = json_decode($json, true);
            return is_array($array) ? $array : [];
        }

        return [];
    }
}

