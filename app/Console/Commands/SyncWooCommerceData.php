<?php

namespace App\Console\Commands;

use App\Services\WooCommerce\Sync\CouponSyncService;
use App\Services\WooCommerce\Sync\CustomerSyncService;
use App\Services\WooCommerce\Sync\OrderSyncService;
use App\Services\WooCommerce\Sync\ProductSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWooCommerceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'woocommerce:sync 
                            {--type=all : Type to sync (all, products, orders, customers, coupons)}
                            {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'مزامنة البيانات من WooCommerce تلقائياً';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');
        
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                $this->error('WooCommerce credentials are not configured.');
                return Command::FAILURE;
            }

            $this->info('بدء المزامنة...');

            if ($type === 'all' || $type === 'products') {
                $this->info('مزامنة المنتجات...');
                $syncService = new ProductSyncService($client);
                $result = $syncService->syncAll();
                $this->info("✓ تم مزامنة {$result['synced']} منتج");
            }

            if ($type === 'all' || $type === 'orders') {
                $this->info('مزامنة الطلبات...');
                $syncService = new OrderSyncService($client);
                $result = $syncService->syncAll();
                $this->info("✓ تم مزامنة {$result['synced']} طلب");
            }

            if ($type === 'all' || $type === 'customers') {
                $this->info('مزامنة العملاء...');
                $syncService = new CustomerSyncService($client);
                $result = $syncService->syncAll();
                $this->info("✓ تم مزامنة {$result['synced']} عميل");
            }

            if ($type === 'all' || $type === 'coupons') {
                $this->info('مزامنة الكوبونات...');
                $syncService = new CouponSyncService($client);
                $result = $syncService->syncAll();
                $this->info("✓ تم مزامنة {$result['synced']} كوبون");
            }

            $this->info('تمت المزامنة بنجاح!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('حدث خطأ أثناء المزامنة: ' . $e->getMessage());
            Log::error('Auto sync error', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}

