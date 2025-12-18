<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\Coupon;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\Log;

class CouponSyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع الكوبونات من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'coupons',
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
            // جلب جميع الكوبونات من WooCommerce
            $coupons = $this->client->getAllPaginated('coupons', ['status' => 'any'], 50);

            $log->update(['total_items' => count($coupons)]);

            foreach ($coupons as $couponData) {
                try {
                    // تحويل stdClass إلى array إذا لزم الأمر
                    $couponArray = $this->toArray($couponData);
                    $this->syncCoupon($couponArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($coupons));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $couponArray = $this->toArray($couponData);
                    $errors[] = [
                        'woo_id' => $couponArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Coupon sync failed', [
                        'woo_id' => $couponArray['id'] ?? null,
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
                'total' => count($coupons),
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
     * مزامنة كوبون واحد
     */
    public function syncCoupon(array $couponData): Coupon
    {
        $data = $this->transformCouponData($couponData);

        return Coupon::updateOrCreate(
            ['woo_id' => $couponData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات الكوبون من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformCouponData(array $coupon): array
    {
        return [
            'woo_id' => $coupon['id'] ?? null,
            'code' => $coupon['code'] ?? '',
            'discount_type' => $coupon['discount_type'] ?? 'fixed_cart',
            'amount' => $this->parsePrice($coupon['amount'] ?? '0'),
            'description' => $coupon['description'] ?? '',
            'date_expires' => isset($coupon['date_expires']) && $coupon['date_expires'] ? $coupon['date_expires'] : null,
            'usage_count' => (int) ($coupon['usage_count'] ?? 0),
            'individual_use' => (bool) ($coupon['individual_use'] ?? false),
            'product_ids' => $this->toArray($coupon['product_ids'] ?? []),
            'excluded_product_ids' => $this->toArray($coupon['excluded_product_ids'] ?? []),
            'usage_limit' => isset($coupon['usage_limit']) ? (int) $coupon['usage_limit'] : null,
            'usage_limit_per_user' => isset($coupon['usage_limit_per_user']) ? (int) $coupon['usage_limit_per_user'] : null,
            'limit_usage_to_x_items' => isset($coupon['limit_usage_to_x_items']) ? (int) $coupon['limit_usage_to_x_items'] : null,
            'free_shipping' => (bool) ($coupon['free_shipping'] ?? false),
            'product_categories' => $this->toArray($coupon['product_categories'] ?? []),
            'excluded_product_categories' => $this->toArray($coupon['excluded_product_categories'] ?? []),
            'exclude_sale_items' => (bool) ($coupon['exclude_sale_items'] ?? false),
            'minimum_amount' => $this->parsePrice($coupon['minimum_amount'] ?? null),
            'maximum_amount' => $this->parsePrice($coupon['maximum_amount'] ?? null),
            'email_restrictions' => $this->toArray($coupon['email_restrictions'] ?? []),
            'used_by' => $this->toArray($coupon['used_by'] ?? []),
            'meta_data' => $this->toArray($coupon['meta_data'] ?? []),
            'woo_created_at' => isset($coupon['date_created']) ? $coupon['date_created'] : now(),
            'woo_updated_at' => isset($coupon['date_modified']) ? $coupon['date_modified'] : now(),
        ];
    }

    protected function parsePrice(?string $price): ?float
    {
        if ($price === null || $price === '') {
            return null;
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

