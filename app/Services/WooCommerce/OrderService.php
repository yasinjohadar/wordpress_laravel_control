<?php

namespace App\Services\WooCommerce;

use App\Services\WooCommerce\WooCommerceClient;
use Exception;

class OrderService
{
    public function __construct(
        protected WooCommerceClient $client
    ) {
    }

    /**
     * تحديث حالة الطلب في WooCommerce
     */
    public function updateStatus(int $wooId, string $status): array
    {
        $validStatuses = ['pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception("حالة الطلب غير صحيحة: {$status}");
        }

        $result = $this->client->put("orders/{$wooId}", [
            'status' => $status,
        ]);

        return $this->toArray($result);
    }

    /**
     * إضافة ملاحظة للطلب
     */
    public function addNote(int $wooId, string $note, bool $customerNote = false): array
    {
        $result = $this->client->post("orders/{$wooId}/notes", [
            'note' => $note,
            'customer_note' => $customerNote,
        ]);

        return $this->toArray($result);
    }

    /**
     * تحديث بيانات الطلب
     */
    public function update(int $wooId, array $orderData): array
    {
        $result = $this->client->put("orders/{$wooId}", $orderData);

        return $this->toArray($result);
    }

    /**
     * حذف الطلب من WooCommerce
     */
    public function delete(int $wooId, bool $force = false): array
    {
        $result = $this->client->delete("orders/{$wooId}", ['force' => $force]);

        return $this->toArray($result);
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

