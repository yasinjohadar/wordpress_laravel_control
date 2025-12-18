<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\Order;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderSyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع الطلبات من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'orders',
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
            // جلب جميع الطلبات من WooCommerce
            $orders = $this->client->getAllPaginated('orders', ['status' => 'any'], 50);

            $log->update(['total_items' => count($orders)]);

            foreach ($orders as $orderData) {
                try {
                    // تحويل stdClass إلى array إذا لزم الأمر
                    $orderArray = $this->toArray($orderData);
                    $this->syncOrder($orderArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($orders));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $orderArray = $this->toArray($orderData);
                    $errors[] = [
                        'woo_id' => $orderArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Order sync failed', [
                        'woo_id' => $orderArray['id'] ?? null,
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
                'total' => count($orders),
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
     * مزامنة طلب واحد
     */
    public function syncOrder(array $orderData): Order
    {
        $data = $this->transformOrderData($orderData);

        return Order::updateOrCreate(
            ['woo_id' => $orderData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات الطلب من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformOrderData(array $order): array
    {
        return [
            'woo_id' => $order['id'] ?? null,
            'order_number' => $order['number'] ?? (string) ($order['id'] ?? ''),
            'status' => $order['status'] ?? 'pending',
            'currency' => $order['currency'] ?? 'SAR',
            'currency_symbol' => $order['currency_symbol'] ?? 'ر.س',
            'total' => $this->parsePrice($order['total'] ?? '0'),
            'subtotal' => $this->parsePrice($order['subtotal'] ?? '0'),
            'total_tax' => $this->parsePrice($order['total_tax'] ?? '0'),
            'shipping_total' => $this->parsePrice($order['shipping_total'] ?? '0'),
            'discount_total' => $this->parsePrice($order['discount_total'] ?? '0'),
            'discount_tax' => $this->parsePrice($order['discount_tax'] ?? '0'),
            'payment_method' => $order['payment_method'] ?? '',
            'payment_method_title' => $order['payment_method_title'] ?? '',
            'transaction_id' => $order['transaction_id'] ?? null,
            'customer_id' => $order['customer_id'] ?? null,
            'customer_ip_address' => $order['customer_ip_address'] ?? null,
            'customer_user_agent' => $order['customer_user_agent'] ?? null,
            'customer_note' => $order['customer_note'] ?? '',
            'billing_address' => $this->toArray($order['billing'] ?? []),
            'shipping_address' => $this->toArray($order['shipping'] ?? []),
            'line_items' => $this->toArray($order['line_items'] ?? []),
            'shipping_lines' => $this->toArray($order['shipping_lines'] ?? []),
            'fee_lines' => $this->toArray($order['fee_lines'] ?? []),
            'coupon_lines' => $this->toArray($order['coupon_lines'] ?? []),
            'refunds' => $this->toArray($order['refunds'] ?? []),
            'meta_data' => $this->toArray($order['meta_data'] ?? []),
            'date_paid' => isset($order['date_paid']) && $order['date_paid'] ? $order['date_paid'] : null,
            'date_completed' => isset($order['date_completed']) && $order['date_completed'] ? $order['date_completed'] : null,
            'woo_created_at' => isset($order['date_created']) ? $order['date_created'] : now(),
            'woo_updated_at' => isset($order['date_modified']) ? $order['date_modified'] : now(),
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

