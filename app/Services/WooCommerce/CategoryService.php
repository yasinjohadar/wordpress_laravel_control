<?php

namespace App\Services\WooCommerce;

use App\Services\WooCommerce\WooCommerceClient;
use Exception;

class CategoryService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * إنشاء فئة جديدة في WooCommerce
     */
    public function create(array $categoryData): array
    {
        $wooCategoryData = $this->transformToWooCommerceFormat($categoryData);
        
        $result = $this->client->post('products/categories', $wooCategoryData);
        
        return $this->toArray($result);
    }

    /**
     * تحديث فئة في WooCommerce
     */
    public function update(int $wooId, array $categoryData): array
    {
        $wooCategoryData = $this->transformToWooCommerceFormat($categoryData);
        
        $result = $this->client->put("products/categories/{$wooId}", $wooCategoryData);
        
        return $this->toArray($result);
    }

    /**
     * حذف فئة من WooCommerce
     */
    public function delete(int $wooId, bool $force = false): array
    {
        $result = $this->client->delete("products/categories/{$wooId}", ['force' => $force]);
        
        return $this->toArray($result);
    }

    /**
     * تحويل بيانات الفئة من تنسيق Laravel إلى تنسيق WooCommerce API
     */
    protected function transformToWooCommerceFormat(array $data): array
    {
        $wooData = [
            'name' => $data['name'] ?? '',
            'slug' => $data['slug'] ?? '',
        ];

        if (isset($data['parent_id']) && $data['parent_id']) {
            $wooData['parent'] = (int) $data['parent_id'];
        }

        if (isset($data['description'])) {
            $wooData['description'] = $data['description'];
        }

        if (isset($data['image']) && $data['image']) {
            $wooData['image'] = [
                'src' => $data['image'],
            ];
        }

        if (isset($data['display'])) {
            $wooData['display'] = $data['display']; // default, products, subcategories, both
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

