<?php

namespace App\Services\WooCommerce;

use App\Services\WooCommerce\WooCommerceClient;
use Exception;

class TagService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * إنشاء علامة جديدة في WooCommerce
     */
    public function create(array $tagData): array
    {
        $wooTagData = $this->transformToWooCommerceFormat($tagData);
        
        $result = $this->client->post('products/tags', $wooTagData);
        
        return $this->toArray($result);
    }

    /**
     * تحديث علامة في WooCommerce
     */
    public function update(int $wooId, array $tagData): array
    {
        $wooTagData = $this->transformToWooCommerceFormat($tagData);
        
        $result = $this->client->put("products/tags/{$wooId}", $wooTagData);
        
        return $this->toArray($result);
    }

    /**
     * حذف علامة من WooCommerce
     */
    public function delete(int $wooId, bool $force = false): array
    {
        $result = $this->client->delete("products/tags/{$wooId}", ['force' => $force]);
        
        return $this->toArray($result);
    }

    /**
     * تحويل بيانات العلامة من تنسيق Laravel إلى تنسيق WooCommerce API
     */
    protected function transformToWooCommerceFormat(array $data): array
    {
        $wooData = [
            'name' => $data['name'] ?? '',
            'slug' => $data['slug'] ?? '',
        ];

        if (isset($data['description'])) {
            $wooData['description'] = $data['description'];
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

