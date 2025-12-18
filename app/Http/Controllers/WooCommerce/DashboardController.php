<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Coupon;
use App\Models\WooCommerce\Customer;
use App\Models\WooCommerce\Order;
use App\Models\WooCommerce\Product;
use App\Services\WooCommerce\Sync\CouponSyncService;
use App\Services\WooCommerce\Sync\CustomerSyncService;
use App\Services\WooCommerce\Sync\OrderSyncService;
use App\Services\WooCommerce\ReportService;
use App\Services\WooCommerce\Sync\ProductSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $client = new WooCommerceClient();
        $connectionStatus = $client->getConnectionStatus();
        $reportService = new ReportService();

        $stats = [
            'orders' => Order::count(),
            'products' => Product::count(),
            'customers' => Customer::count(),
            'coupons' => Coupon::count(),
            'today_orders' => Order::whereDate('woo_created_at', today())->count(),
        ];

        // إحصائيات متقدمة
        $todaySales = $reportService->getSalesReport('today');
        $monthSales = $reportService->getSalesReport('month');
        $topProducts = $reportService->getTopSellingProducts(5);
        $ordersByStatus = $reportService->getOrdersByStatus();

        $recentOrders = Order::orderByDesc('woo_created_at')->limit(10)->get();

        return view('admin.woocommerce.dashboard', compact(
            'connectionStatus',
            'stats',
            'recentOrders',
            'todaySales',
            'monthSales',
            'topProducts',
            'ordersByStatus'
        ));
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $type = $request->get('type', 'all');
            $results = [];

            if ($type === 'all' || $type === 'products') {
                $productSync = new ProductSyncService($client);
                $results['products'] = $productSync->syncAll();
            }

            if ($type === 'all' || $type === 'orders') {
                $orderSync = new OrderSyncService($client);
                $results['orders'] = $orderSync->syncAll();
            }

            if ($type === 'all' || $type === 'customers') {
                $customerSync = new CustomerSyncService($client);
                $results['customers'] = $customerSync->syncAll();
            }

            if ($type === 'all' || $type === 'coupons') {
                $couponSync = new CouponSyncService($client);
                $results['coupons'] = $couponSync->syncAll();
            }

            $message = 'تمت المزامنة بنجاح: ';
            $parts = [];
            foreach ($results as $key => $result) {
                $parts[] = "{$result['synced']} {$key}";
            }
            $message .= implode(', ', $parts);

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Global sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }

    public function testConnection()
    {
        try {
            $client = new WooCommerceClient();
            $status = $client->getConnectionStatus();

            if ($status['connected']) {
                return response()->json([
                    'success' => true,
                    'message' => $status['message'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $status['message'],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}


