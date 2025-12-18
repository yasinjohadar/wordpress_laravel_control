<?php

namespace App\Services\WooCommerce;

use App\Services\WooCommerce\WooCommerceClient;
use Exception;

class ProductService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * إنشاء منتج جديد في WooCommerce
     */
    public function create(array $productData): array
    {
        $wooProductData = $this->transformToWooCommerceFormat($productData);
        
        $result = $this->client->post('products', $wooProductData);
        
        return $this->toArray($result);
    }

    /**
     * تحديث منتج في WooCommerce
     */
    public function update(int $wooId, array $productData): array
    {
        $wooProductData = $this->transformToWooCommerceFormat($productData);
        
        $result = $this->client->put("products/{$wooId}", $wooProductData);
        
        return $this->toArray($result);
    }

    /**
     * حذف منتج من WooCommerce
     */
    public function delete(int $wooId, bool $force = false): array
    {
        $result = $this->client->delete("products/{$wooId}", ['force' => $force]);
        
        return $this->toArray($result);
    }

    /**
     * تحويل بيانات المنتج من تنسيق Laravel إلى تنسيق WooCommerce API
     */
    protected function transformToWooCommerceFormat(array $data): array
    {
        $wooData = [
            'name' => $data['name'] ?? '',
            'type' => $data['type'] ?? 'simple',
            'status' => $data['status'] ?? 'draft',
            'featured' => (bool) ($data['featured'] ?? false),
            'catalog_visibility' => $data['catalog_visibility'] ?? 'visible',
            'description' => $data['description'] ?? '',
            'short_description' => $data['short_description'] ?? '',
            'sku' => $data['sku'] ?? '',
            'regular_price' => (string) ($data['regular_price'] ?? '0'),
            'sale_price' => isset($data['sale_price']) && $data['sale_price'] ? (string) $data['sale_price'] : '',
            'tax_status' => $data['tax_status'] ?? 'taxable',
            'tax_class' => $data['tax_class'] ?? '',
            'manage_stock' => (bool) ($data['manage_stock'] ?? false),
            'stock_quantity' => isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : null,
            'stock_status' => $data['stock_status'] ?? 'instock',
            'backorders' => $data['backorders'] ?? ($data['stock_status'] === 'onbackorder' ? 'yes' : 'no'),
            'virtual' => (bool) ($data['virtual'] ?? false),
            'downloadable' => (bool) ($data['downloadable'] ?? false),
        ];

        // الوزن والأبعاد
        if (isset($data['weight'])) {
            $wooData['weight'] = (string) $data['weight'];
        }

        if (isset($data['dimensions'])) {
            $dimensions = is_array($data['dimensions']) ? $data['dimensions'] : [];
            $wooData['dimensions'] = [
                'length' => (string) ($dimensions['length'] ?? ''),
                'width' => (string) ($dimensions['width'] ?? ''),
                'height' => (string) ($dimensions['height'] ?? ''),
            ];
        }

        // الفئات
        if (isset($data['category_ids']) && is_array($data['category_ids'])) {
            $wooData['categories'] = array_map(function ($id) {
                return ['id' => (int) $id];
            }, $data['category_ids']);
        }

        // العلامات
        if (isset($data['tag_ids']) && is_array($data['tag_ids'])) {
            $wooData['tags'] = array_map(function ($id) {
                return ['id' => (int) $id];
            }, $data['tag_ids']);
        }

        // الصور
        if (isset($data['images']) && is_array($data['images'])) {
            $wooData['images'] = array_map(function ($image) {
                if (is_string($image)) {
                    return ['src' => $image];
                }
                return [
                    'src' => $image['src'] ?? '',
                    'name' => $image['name'] ?? '',
                    'alt' => $image['alt'] ?? '',
                ];
            }, $data['images']);
        }

        return $wooData;
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
            // استخدام json_decode/json_encode للتحويل
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

