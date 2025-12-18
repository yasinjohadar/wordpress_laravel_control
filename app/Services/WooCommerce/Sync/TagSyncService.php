<?php

namespace App\Services\WooCommerce\Sync;

use App\Models\WooCommerce\ProductTag;
use App\Models\WooCommerce\SyncLog;
use App\Services\WooCommerce\WooCommerceClient;
use Exception;
use Illuminate\Support\Facades\Log;

class TagSyncService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * مزامنة جميع العلامات من WooCommerce
     */
    public function syncAll(?callable $progressCallback = null): array
    {
        $log = SyncLog::create([
            'type' => 'tags',
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
            $tags = $this->client->getAllPaginated('products/tags', [], 50);

            $log->update(['total_items' => count($tags)]);

            foreach ($tags as $tagData) {
                try {
                    $tagArray = $this->toArray($tagData);
                    $this->syncTag($tagArray);
                    $synced++;

                    if ($progressCallback) {
                        $progressCallback($synced, count($tags));
                    }
                } catch (Exception $e) {
                    $failed++;
                    $tagArray = $this->toArray($tagData);
                    $errors[] = [
                        'woo_id' => $tagArray['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];

                    Log::error('Tag sync failed', [
                        'woo_id' => $tagArray['id'] ?? null,
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
                'total' => count($tags),
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
     * مزامنة علامة واحدة
     */
    public function syncTag(array $tagData): ProductTag
    {
        $data = $this->transformTagData($tagData);

        return ProductTag::updateOrCreate(
            ['woo_id' => $tagData['id']],
            $data
        );
    }

    /**
     * تحويل بيانات العلامة من WooCommerce إلى تنسيق قاعدة البيانات المحلية
     */
    protected function transformTagData(array $tagData): array
    {
        return [
            'woo_id' => $tagData['id'],
            'name' => $tagData['name'] ?? '',
            'slug' => $tagData['slug'] ?? '',
            'description' => $tagData['description'] ?? null,
            'count' => $tagData['count'] ?? 0,
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

