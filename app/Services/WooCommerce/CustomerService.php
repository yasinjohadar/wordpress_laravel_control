<?php

namespace App\Services\WooCommerce;

use App\Services\WooCommerce\WooCommerceClient;
use Exception;

class CustomerService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * إنشاء عميل جديد في WooCommerce
     */
    public function create(array $customerData): array
    {
        $wooCustomerData = $this->transformToWooCommerceFormat($customerData);
        
        $result = $this->client->post('customers', $wooCustomerData);
        
        return $this->toArray($result);
    }

    /**
     * تحديث عميل في WooCommerce
     */
    public function update(int $wooId, array $customerData): array
    {
        // إزالة username من البيانات لأن WooCommerce لا يسمح بتعديله
        unset($customerData['username']);
        
        $wooCustomerData = $this->transformToWooCommerceFormat($customerData);
        
        $result = $this->client->put("customers/{$wooId}", $wooCustomerData);
        
        return $this->toArray($result);
    }

    /**
     * حذف عميل من WooCommerce
     */
    public function delete(int $wooId, bool $force = false): array
    {
        $result = $this->client->delete("customers/{$wooId}", ['force' => $force]);
        
        return $this->toArray($result);
    }

    /**
     * تحويل بيانات العميل من تنسيق Laravel إلى تنسيق WooCommerce API
     */
    protected function transformToWooCommerceFormat(array $data): array
    {
        $wooData = [
            'email' => $data['email'] ?? '',
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
        ];

        // إضافة username فقط عند الإنشاء (ليس عند التحديث)
        if (isset($data['username']) && !empty($data['username'])) {
            $wooData['username'] = $data['username'];
        }

        // إضافة كلمة المرور فقط إذا كانت موجودة (للتحديث)
        if (isset($data['password']) && !empty($data['password'])) {
            $wooData['password'] = $data['password'];
        }

        // عنوان الفواتير
        if (isset($data['billing'])) {
            $billing = is_array($data['billing']) ? $data['billing'] : [];
            $wooData['billing'] = [
                'first_name' => $billing['first_name'] ?? $data['first_name'] ?? '',
                'last_name' => $billing['last_name'] ?? $data['last_name'] ?? '',
                'company' => $billing['company'] ?? '',
                'address_1' => $billing['address_1'] ?? '',
                'address_2' => $billing['address_2'] ?? '',
                'city' => $billing['city'] ?? '',
                'state' => $billing['state'] ?? '',
                'postcode' => $billing['postcode'] ?? '',
                'country' => $billing['country'] ?? 'SA',
                'email' => $billing['email'] ?? $data['email'] ?? '',
                'phone' => $billing['phone'] ?? '',
            ];
        }

        // عنوان الشحن
        if (isset($data['shipping'])) {
            $shipping = is_array($data['shipping']) ? $data['shipping'] : [];
            $wooData['shipping'] = [
                'first_name' => $shipping['first_name'] ?? $data['first_name'] ?? '',
                'last_name' => $shipping['last_name'] ?? $data['last_name'] ?? '',
                'company' => $shipping['company'] ?? '',
                'address_1' => $shipping['address_1'] ?? '',
                'address_2' => $shipping['address_2'] ?? '',
                'city' => $shipping['city'] ?? '',
                'state' => $shipping['state'] ?? '',
                'postcode' => $shipping['postcode'] ?? '',
                'country' => $shipping['country'] ?? 'SA',
            ];
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

