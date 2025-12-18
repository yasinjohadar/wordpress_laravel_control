<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\Product;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع المنتجات من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'products',
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
            // جلب جميع المنتجات من WooCommerce
            $products = $this->client->getAllPaginated('products', ['status' => 'any'], 50);

            $log->update(['total_items' => count($products)]);

            foreach ($products as $productData) {
                try {
                    // تحويل stdClass إلى array إذا لزم الأمر
                    $productArray = $this->toArray($productData);
                    $this->syncProduct($productArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($products));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $productArray = $this->toArray($productData);
                    $errors[] = [
                        'woo_id' => $productArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Product sync failed', [
                        'woo_id' => $productArray['id'] ?? null,
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
                'total' => count($products),
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
     * مزامنة منتج واحد
     */
    public function syncProduct(array $productData): Product
    {
        $data = $this->transformProductData($productData);

        return Product::updateOrCreate(
            ['woo_id' => $productData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات المنتج من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformProductData(array $product): array
    {
        $dimensions = $this->toArray($product['dimensions'] ?? []);
        $categories = $this->toArray($product['categories'] ?? []);
        $tags = $this->toArray($product['tags'] ?? []);
        $images = $this->toArray($product['images'] ?? []);

        return [
            'woo_id' => $product['id'] ?? null,
            'name' => $product['name'] ?? '',
            'slug' => $product['slug'] ?? '',
            'type' => $product['type'] ?? 'simple',
            'status' => $product['status'] ?? 'draft',
            'featured' => (bool) ($product['featured'] ?? false),
            'catalog_visibility' => $product['catalog_visibility'] ?? 'visible',
            'description' => $product['description'] ?? '',
            'short_description' => $product['short_description'] ?? '',
            'sku' => $product['sku'] ?? null,
            'price' => $this->parsePrice($product['price'] ?? '0'),
            'regular_price' => $this->parsePrice($product['regular_price'] ?? '0'),
            'sale_price' => $this->parsePrice($product['sale_price'] ?? null),
            'on_sale' => !empty($product['sale_price']) && (float)($product['sale_price'] ?? 0) > 0,
            'purchasable' => (bool) ($product['purchasable'] ?? true),
            'total_sales' => (int) ($product['total_sales'] ?? 0),
            'virtual' => (bool) ($product['virtual'] ?? false),
            'downloadable' => (bool) ($product['downloadable'] ?? false),
            'tax_status' => $product['tax_status'] ?? 'taxable',
            'tax_class' => $product['tax_class'] ?? '',
            'manage_stock' => (bool) ($product['manage_stock'] ?? false),
            'stock_quantity' => isset($product['stock_quantity']) ? (int) $product['stock_quantity'] : null,
            'stock_status' => $product['stock_status'] ?? 'instock',
            'backorders' => $product['backorders'] ?? 'no',
            'weight' => $product['weight'] ?? null,
            'dimensions' => [
                'length' => $dimensions['length'] ?? null,
                'width' => $dimensions['width'] ?? null,
                'height' => $dimensions['height'] ?? null,
            ],
            'categories' => array_map(fn($cat) => [
                'id' => $this->toArray($cat)['id'] ?? null,
                'name' => $this->toArray($cat)['name'] ?? '',
            ], $categories),
            'tags' => array_map(fn($tag) => [
                'id' => $this->toArray($tag)['id'] ?? null,
                'name' => $this->toArray($tag)['name'] ?? '',
            ], $tags),
            'images' => array_map(fn($img) => [
                'id' => $this->toArray($img)['id'] ?? null,
                'src' => $this->toArray($img)['src'] ?? '',
                'name' => $this->toArray($img)['name'] ?? '',
                'alt' => $this->toArray($img)['alt'] ?? '',
            ], $images),
            'attributes' => $this->toArray($product['attributes'] ?? []),
            'variations' => $this->toArray($product['variations'] ?? []),
            'meta_data' => $this->toArray($product['meta_data'] ?? []),
            'woo_created_at' => isset($product['date_created']) ? $product['date_created'] : now(),
            'woo_updated_at' => isset($product['date_modified']) ? $product['date_modified'] : now(),
        ];
    }

    protected function parsePrice(?string $price): float
    {
        if ($price === null || $price === '') {
            return 0.0;
        }

        return (float) $price;
    }

    /**
     * تحويل stdClass أو array إلى array
     */
    protected function toArray(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_object($data)) {
            return json_decode(json_encode($data), true);
        }

        return [];
    }
}

