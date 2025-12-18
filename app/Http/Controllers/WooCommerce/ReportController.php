<?php

namespace App\Http\Controllers\WooCommerce;

use App\Http\Controllers\Controller;
use App\Services\WooCommerce\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {
    }

    /**
     * صفحة التقارير الرئيسية
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $salesReport = $this->reportService->getSalesReport($period);
        $chartData = $this->reportService->getSalesChartData($period);
        $topProducts = $this->reportService->getTopSellingProducts(10);
        $topCustomers = $this->reportService->getTopCustomers(10);
        $ordersByStatus = $this->reportService->getOrdersByStatus();
        $monthlySales = $this->reportService->getMonthlySales();
        $comparison = $this->reportService->comparePeriods($period, $period);
        $productsPerformance = $this->reportService->getProductsPerformance();

        return view('admin.woocommerce.reports.index', compact(
            'salesReport',
            'chartData',
            'topProducts',
            'topCustomers',
            'ordersByStatus',
            'monthlySales',
            'comparison',
            'productsPerformance',
            'period'
        ));
    }

    /**
     * تقرير المبيعات
     */
    public function sales(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $salesReport = $this->reportService->getSalesReport($period);
        $chartData = $this->reportService->getSalesChartData($period);
        $monthlySales = $this->reportService->getMonthlySales();
        $comparison = $this->reportService->comparePeriods($period, $period);

        return view('admin.woocommerce.reports.sales', compact(
            'salesReport',
            'chartData',
            'monthlySales',
            'comparison',
            'period'
        ));
    }

    /**
     * تقرير المنتجات
     */
    public function products()
    {
        $topProducts = $this->reportService->getTopSellingProducts(20);
        $productsPerformance = $this->reportService->getProductsPerformance();

        return view('admin.woocommerce.reports.products', compact(
            'topProducts',
            'productsPerformance'
        ));
    }

    /**
     * تقرير العملاء
     */
    public function customers()
    {
        $topCustomers = $this->reportService->getTopCustomers(20);

        return view('admin.woocommerce.reports.customers', compact('topCustomers'));
    }

    /**
     * API endpoint للحصول على بيانات الرسم البياني
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'month');
        $chartData = $this->reportService->getSalesChartData($period);

        return response()->json($chartData);
    }
}

