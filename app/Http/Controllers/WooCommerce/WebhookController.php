<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Coupon;
use App\Models\WooCommerce\Customer;
use App\Models\WooCommerce\Order;
use App\Models\WooCommerce\Product;
use App\Models\WooCommerce\ProductCategory;
use App\Models\WooCommerce\ProductTag;
use App\Services\WooCommerce\Sync\CouponSyncService;
use App\Services\WooCommerce\Sync\CustomerSyncService;
use App\Services\WooCommerce\Sync\OrderSyncService;
use App\Services\WooCommerce\Sync\ProductSyncService;
use App\Services\WooCommerce\Sync\CategorySyncService;
use App\Services\WooCommerce\Sync\TagSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * معالجة Webhooks من WooCommerce
     * 
     * يتم استدعاء هذا المسار من WooCommerce عند حدوث تغييرات
     */
    public function handle(Request $request)
    {
        try {
            // التحقق من التوقيع (اختياري - للأمان)
            $secret = config('woocommerce.webhook_secret');
            if ($secret && $request->header('X-WC-Webhook-Signature')) {
                // يمكن إضافة التحقق من التوقيع هنا
            }

            // قراءة headers من WooCommerce
            $topic = $request->header('X-WC-Webhook-Topic') ?? $request->header('x-wc-webhook-topic');
            
            // تحليل topic للحصول على resource و event
            // مثال: "product.created" => resource: "product", event: "created"
            $resource = null;
            $event = null;
            
            if ($topic) {
                $parts = explode('.', $topic);
                if (count($parts) >= 2) {
                    $resource = $parts[0]; // product, order, customer, coupon
                    $event = $parts[1]; // created, updated, deleted
                }
            }

            // إذا لم يتم العثور على resource من topic، جرب header مباشرة
            if (!$resource) {
                $resource = $request->header('X-WC-Webhook-Resource') ?? $request->header('x-wc-webhook-resource');
            }
            
            if (!$event) {
                $event = $request->header('X-WC-Webhook-Event') ?? $request->header('x-wc-webhook-event');
            }

            Log::info('WooCommerce Webhook received', [
                'topic' => $topic,
                'event' => $event,
                'resource' => $resource,
                'headers' => $request->headers->all(),
            ]);

            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                Log::warning('WooCommerce webhook received but client not configured');
                return response()->json(['error' => 'WooCommerce not configured'], 400);
            }

            // قراءة البيانات من body
            $data = $request->all();
            
            // إذا كانت البيانات فارغة، جرب json_decode مباشرة
            if (empty($data)) {
                $json = $request->getContent();
                if ($json) {
                    $data = json_decode($json, true) ?? [];
                }
            }

            $id = $data['id'] ?? null;

            if (!$id) {
                Log::warning('Webhook received without ID', ['data' => $data]);
                return response()->json(['error' => 'No ID provided'], 400);
            }

            // معالجة الأحداث المختلفة
            switch ($resource) {
                case 'product':
                    $this->handleProductWebhook($client, $event, $id, $data);
                    break;
                
                case 'order':
                    $this->handleOrderWebhook($client, $event, $id, $data);
                    break;
                
                case 'customer':
                    $this->handleCustomerWebhook($client, $event, $id, $data);
                    break;
                
                case 'coupon':
                    $this->handleCouponWebhook($client, $event, $id, $data);
                    break;
                
                case 'product_cat':
                case 'product_category':
                    $this->handleCategoryWebhook($client, $event, $id, $data);
                    break;
                
                case 'product_tag':
                    $this->handleTagWebhook($client, $event, $id, $data);
                    break;
                
                default:
                    Log::warning('Unknown webhook resource', [
                        'resource' => $resource,
                        'topic' => $topic,
                        'data_keys' => array_keys($data),
                    ]);
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function handleProductWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new ProductSyncService($client);
        
        if ($event === 'deleted') {
            // حذف المنتج من قاعدة البيانات المحلية
            Product::where('woo_id', $id)->delete();
            Log::info("Product deleted: {$id}");
        } else {
            // تحديث أو إنشاء المنتج
            $productData = $this->toArray($data);
            $syncService->syncProduct($productData);
            Log::info("Product synced: {$id} (event: {$event})");
        }
    }

    protected function handleOrderWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new OrderSyncService($client);
        
        if ($event === 'deleted') {
            Order::where('woo_id', $id)->delete();
            Log::info("Order deleted: {$id}");
        } else {
            $orderData = $this->toArray($data);
            $syncService->syncOrder($orderData);
            Log::info("Order synced: {$id} (event: {$event})");
        }
    }

    protected function handleCustomerWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new CustomerSyncService($client);
        
        if ($event === 'deleted') {
            Customer::where('woo_id', $id)->delete();
            Log::info("Customer deleted: {$id}");
        } else {
            $customerData = $this->toArray($data);
            $syncService->syncCustomer($customerData);
            Log::info("Customer synced: {$id} (event: {$event})");
        }
    }

    protected function handleCouponWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new CouponSyncService($client);
        
        if ($event === 'deleted') {
            Coupon::where('woo_id', $id)->delete();
            Log::info("Coupon deleted: {$id}");
        } else {
            $couponData = $this->toArray($data);
            $syncService->syncCoupon($couponData);
            Log::info("Coupon synced: {$id} (event: {$event})");
        }
    }

    protected function handleCategoryWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new CategorySyncService($client);
        
        if ($event === 'deleted') {
            ProductCategory::where('woo_id', $id)->delete();
            Log::info("Category deleted: {$id}");
        } else {
            $categoryData = $this->toArray($data);
            $syncService->syncCategory($categoryData);
            Log::info("Category synced: {$id} (event: {$event})");
        }
    }

    protected function handleTagWebhook(WooCommerceClient $client, string $event, int $id, array $data): void
    {
        $syncService = new TagSyncService($client);
        
        if ($event === 'deleted') {
            ProductTag::where('woo_id', $id)->delete();
            Log::info("Tag deleted: {$id}");
        } else {
            $tagData = $this->toArray($data);
            $syncService->syncTag($tagData);
            Log::info("Tag synced: {$id} (event: {$event})");
        }
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

