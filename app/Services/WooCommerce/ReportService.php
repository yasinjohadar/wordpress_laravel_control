<?php

namespace App\Services\WooCommerce;

use App\Models\WooCommerce\Customer;
use App\Models\WooCommerce\Order;
use App\Models\WooCommerce\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * إحصائيات المبيعات حسب الفترة
     */
    public function getSalesReport(string $period = 'month'): array
    {
        $startDate = match ($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfMonth(),
        };

        $endDate = Carbon::now();

        $orders = Order::whereBetween('woo_created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalTax = $orders->sum('total_tax');
        $totalShipping = $orders->sum('shipping_total');
        $totalDiscount = $orders->sum('discount_total');

        return [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'total_tax' => $totalTax,
            'total_shipping' => $totalShipping,
            'total_discount' => $totalDiscount,
        ];
    }

    /**
     * بيانات الرسم البياني للمبيعات حسب التاريخ
     */
    public function getSalesChartData(string $period = 'month', int $days = 30): array
    {
        $startDate = match ($period) {
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            'year' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays($days),
        };

        $endDate = Carbon::now();

        $orders = Order::whereBetween('woo_created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(woo_created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $revenueData = [];
        $ordersData = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d/m');
            
            $dayData = $orders->firstWhere('date', $dateStr);
            $revenueData[] = $dayData ? (float) $dayData->revenue : 0;
            $ordersData[] = $dayData ? (int) $dayData->orders_count : 0;
            
            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'orders' => $ordersData,
        ];
    }

    /**
     * المنتجات الأكثر مبيعاً
     */
    public function getTopSellingProducts(int $limit = 10): array
    {
        $products = Product::where('total_sales', '>', 0)
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get(['id', 'name', 'total_sales', 'price', 'regular_price']);

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sales' => $product->total_sales,
                'revenue' => $product->total_sales * ($product->price ?? $product->regular_price ?? 0),
                'price' => $product->price ?? $product->regular_price ?? 0,
            ];
        })->toArray();
    }

    /**
     * العملاء الأكثر شراءً
     */
    public function getTopCustomers(int $limit = 10): array
    {
        $customers = Customer::where('is_paying_customer', true)
            ->where('orders_count', '>', 0)
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get(['id', 'email', 'first_name', 'last_name', 'orders_count', 'total_spent']);

        return $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'email' => $customer->email,
                'orders_count' => $customer->orders_count ?? 0,
                'total_spent' => $customer->total_spent ?? 0,
            ];
        })->toArray();
    }

    /**
     * إحصائيات الطلبات حسب الحالة
     */
    public function getOrdersByStatus(): array
    {
        $statuses = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'pending' => '#f7a556',
            'processing' => '#0162e8',
            'on-hold' => '#f93a5a',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            'refunded' => '#6b7280',
            'failed' => '#dc2626',
        ];

        foreach ($statuses as $status) {
            $labels[] = $this->translateStatus($status->status);
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors),
        ];
    }

    /**
     * إحصائيات المبيعات حسب الشهر (آخر 12 شهر)
     */
    public function getMonthlySales(): array
    {
        $months = [];
        $revenue = [];
        $orders = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $monthOrders = Order::whereBetween('woo_created_at', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'cancelled')
                ->get();

            $months[] = $date->format('M Y');
            $revenue[] = $monthOrders->sum('total');
            $orders[] = $monthOrders->count();
        }

        return [
            'labels' => $months,
            'revenue' => $revenue,
            'orders' => $orders,
        ];
    }

    /**
     * مقارنة الفترات الزمنية
     */
    public function comparePeriods(string $currentPeriod = 'month', string $previousPeriod = 'month'): array
    {
        $current = $this->getSalesReport($currentPeriod);
        
        $previousStartDate = match ($previousPeriod) {
            'today' => Carbon::yesterday(),
            'week' => Carbon::now()->subWeek()->startOfWeek(),
            'month' => Carbon::now()->subMonth()->startOfMonth(),
            'year' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::now()->subMonth()->startOfMonth(),
        };

        $previousEndDate = match ($previousPeriod) {
            'today' => Carbon::yesterday()->endOfDay(),
            'week' => Carbon::now()->subWeek()->endOfWeek(),
            'month' => Carbon::now()->subMonth()->endOfMonth(),
            'year' => Carbon::now()->subYear()->endOfYear(),
            default => Carbon::now()->subMonth()->endOfMonth(),
        };

        $previousOrders = Order::whereBetween('woo_created_at', [$previousStartDate, $previousEndDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $previous = [
            'total_revenue' => $previousOrders->sum('total'),
            'total_orders' => $previousOrders->count(),
        ];

        $revenueChange = $previous['total_revenue'] > 0 
            ? (($current['total_revenue'] - $previous['total_revenue']) / $previous['total_revenue']) * 100 
            : 0;

        $ordersChange = $previous['total_orders'] > 0 
            ? (($current['total_orders'] - $previous['total_orders']) / $previous['total_orders']) * 100 
            : 0;

        return [
            'current' => $current,
            'previous' => $previous,
            'revenue_change' => round($revenueChange, 2),
            'orders_change' => round($ordersChange, 2),
        ];
    }

    /**
     * المنتجات قليلة/عالية المبيعات
     */
    public function getProductsPerformance(int $lowThreshold = 10): array
    {
        $allProducts = Product::where('status', 'publish')->get();
        
        $lowSales = $allProducts->where('total_sales', '<', $lowThreshold)
            ->sortBy('total_sales')
            ->take(10)
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sales' => $product->total_sales ?? 0,
                    'stock' => $product->stock_quantity ?? 0,
                ];
            })
            ->values()
            ->toArray();

        $highSales = $allProducts->where('total_sales', '>=', $lowThreshold)
            ->sortByDesc('total_sales')
            ->take(10)
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sales' => $product->total_sales ?? 0,
                    'revenue' => ($product->total_sales ?? 0) * ($product->price ?? $product->regular_price ?? 0),
                ];
            })
            ->values()
            ->toArray();

        return [
            'low_sales' => $lowSales,
            'high_sales' => $highSales,
        ];
    }

    /**
     * ترجمة حالة الطلب
     */
    protected function translateStatus(string $status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'on-hold' => 'معلق',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            'failed' => 'فاشل',
            default => $status,
        };
    }
}

