<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\Customer;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomerSyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع العملاء من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'customers',
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
            // جلب جميع العملاء من WooCommerce
            $customers = $this->client->getAllPaginated('customers', [], 50);

            $log->update(['total_items' => count($customers)]);

            foreach ($customers as $customerData) {
                try {
                    // تحويل stdClass إلى array إذا لزم الأمر
                    $customerArray = $this->toArray($customerData);
                    $this->syncCustomer($customerArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($customers));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $customerArray = $this->toArray($customerData);
                    $errors[] = [
                        'woo_id' => $customerArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Customer sync failed', [
                        'woo_id' => $customerArray['id'] ?? null,
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
                'total' => count($customers),
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
     * مزامنة عميل واحد
     */
    public function syncCustomer(array $customerData): Customer
    {
        $data = $this->transformCustomerData($customerData);

        return Customer::updateOrCreate(
            ['woo_id' => $customerData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات العميل من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformCustomerData(array $customer): array
    {
        return [
            'woo_id' => $customer['id'] ?? null,
            'email' => $customer['email'] ?? '',
            'first_name' => $customer['first_name'] ?? '',
            'last_name' => $customer['last_name'] ?? '',
            'username' => $customer['username'] ?? '',
            'role' => $customer['role'] ?? 'customer',
            'avatar_url' => $customer['avatar_url'] ?? null,
            'billing_address' => $this->toArray($customer['billing'] ?? []),
            'shipping_address' => $this->toArray($customer['shipping'] ?? []),
            'is_paying_customer' => (bool) ($customer['is_paying_customer'] ?? false),
            'orders_count' => (int) ($customer['orders_count'] ?? 0),
            'total_spent' => $this->parsePrice($customer['total_spent'] ?? '0'),
            'meta_data' => $this->toArray($customer['meta_data'] ?? []),
            'woo_created_at' => isset($customer['date_created']) ? $customer['date_created'] : now(),
            'woo_updated_at' => isset($customer['date_modified']) ? $customer['date_modified'] : now(),
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

