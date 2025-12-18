<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Models\WooCommerce\Order;
use App\Services\WooCommerce\OrderService;
use App\Services\WooCommerce\Sync\OrderSyncService;
use App\Services\WooCommerce\WooCommerceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');

        // البحث
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_id', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // فلترة حسب التاريخ
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('woo_created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('woo_created_at', '<=', $dateTo);
        }

        // فلترة حسب المبلغ
        if ($minAmount = $request->get('min_amount')) {
            $query->where('total', '>=', $minAmount);
        }

        if ($maxAmount = $request->get('max_amount')) {
            $query->where('total', '<=', $maxAmount);
        }

        // فلترة حسب العميل
        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $orders = $query->orderByDesc('woo_created_at')->paginate(15);

        return view('admin.woocommerce.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('customer')->findOrFail($id);
        
        // محاولة جلب أحدث بيانات من WooCommerce إذا كان متاحاً
        $freshData = null;
        try {
            $client = new WooCommerceClient();
            if ($client->isConfigured() && $order->woo_id) {
                $freshData = $client->get("orders/{$order->woo_id}");
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh order data', [
                'order_id' => $id,
                'woo_id' => $order->woo_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.woocommerce.orders.show', compact('order', 'freshData'));
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->woo_id) {
            return redirect()->back()->with('error', 'لا يمكن تحديث هذا الطلب لأنه غير مرتبط بـ WooCommerce.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,on-hold,completed,cancelled,refunded,failed',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $orderService = new OrderService($client);
            $wooOrder = $orderService->updateStatus($order->woo_id, $validated['status']);

            // مزامنة الطلب المحدث
            $syncService = new OrderSyncService($client);
            $syncService->syncOrder($wooOrder);

            return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
        } catch (\Exception $e) {
            Log::error('Order status update error', ['order_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة الطلب: ' . $e->getMessage());
        }
    }

    /**
     * إضافة ملاحظة للطلب
     */
    public function addNote(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->woo_id) {
            return redirect()->back()->with('error', 'لا يمكن إضافة ملاحظة لهذا الطلب لأنه غير مرتبط بـ WooCommerce.');
        }

        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'customer_note' => 'boolean',
        ]);

        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $orderService = new OrderService($client);
            $orderService->addNote($order->woo_id, $validated['note'], $validated['customer_note'] ?? false);

            // مزامنة الطلب للحصول على الملاحظات الجديدة
            $syncService = new OrderSyncService($client);
            $freshData = $client->get("orders/{$order->woo_id}");
            $syncService->syncOrder($freshData);

            return redirect()->back()->with('success', 'تم إضافة الملاحظة بنجاح.');
        } catch (\Exception $e) {
            Log::error('Order note add error', ['order_id' => $id, 'error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الملاحظة: ' . $e->getMessage());
        }
    }

    /**
     * تصدير الطلبات إلى Excel/CSV
     */
    public function export(Request $request)
    {
        $query = Order::with('customer');

        // تطبيق نفس الفلاتر من index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('woo_created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('woo_created_at', '<=', $dateTo);
        }

        $orders = $query->orderByDesc('woo_created_at')->get();

        $format = $request->get('format', 'csv'); // csv or excel

        if ($format === 'excel') {
            // TODO: Implement Excel export using Laravel Excel package
            return redirect()->back()->with('error', 'تصدير Excel غير متاح حالياً. استخدم CSV.');
        }

        // CSV Export
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'رقم الطلب',
                'الحالة',
                'العميل',
                'البريد الإلكتروني',
                'الإجمالي',
                'الضريبة',
                'الشحن',
                'الخصم',
                'تاريخ الطلب',
                'طريقة الدفع',
            ]);

            // Data
            foreach ($orders as $order) {
                $customer = $order->customer;
                fputcsv($file, [
                    $order->order_number,
                    $order->status,
                    $customer ? ($customer->first_name . ' ' . $customer->last_name) : 'N/A',
                    $customer ? $customer->email : 'N/A',
                    $order->total,
                    $order->total_tax,
                    $order->shipping_total,
                    $order->discount_total,
                    $order->woo_created_at?->format('Y-m-d H:i:s'),
                    $order->payment_method_title ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * طباعة الفاتورة
     */
    public function invoice($id)
    {
        $order = Order::with('customer')->findOrFail($id);

        return view('admin.woocommerce.orders.invoice', compact('order'));
    }

    public function sync(Request $request)
    {
        try {
            $client = new WooCommerceClient();
            
            if (!$client->isConfigured()) {
                return redirect()->back()->with('error', 'WooCommerce credentials are not configured.');
            }

            $syncService = new OrderSyncService($client);
            $result = $syncService->syncAll();

            return redirect()->back()->with('success', "تم مزامنة {$result['synced']} طلب بنجاح.");
        } catch (\Exception $e) {
            Log::error('Order sync error', ['error' => $e->getMessage()]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء المزامنة: ' . $e->getMessage());
        }
    }
}


