@extends('admin.layouts.master')

@section('page-title')
    تقرير المبيعات
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير المبيعات</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.reports.index') }}" class="btn btn-primary">
                        <i class="fe fe-arrow-right"></i> العودة للتقارير
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Period Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('woocommerce.reports.sales') }}" class="d-flex align-items-center gap-3">
                        <label class="mb-0">الفترة:</label>
                        <select name="period" class="form-select" style="width: auto;" onchange="this.form.submit()">
                            <option value="today" {{ $period === 'today' ? 'selected' : '' }}>اليوم</option>
                            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>هذا الشهر</option>
                            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>هذه السنة</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-primary-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي الإيرادات</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($salesReport['total_revenue'], 2) }} ر.س</h4>
                            @if($comparison['revenue_change'] != 0)
                                <small class="text-fixed-white">
                                    <i class="fe fe-{{ $comparison['revenue_change'] > 0 ? 'trending-up' : 'trending-down' }}"></i>
                                    {{ abs($comparison['revenue_change']) }}%
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-success-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي الطلبات</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ $salesReport['total_orders'] }}</h4>
                            @if($comparison['orders_change'] != 0)
                                <small class="text-fixed-white">
                                    <i class="fe fe-{{ $comparison['orders_change'] > 0 ? 'trending-up' : 'trending-down' }}"></i>
                                    {{ abs($comparison['orders_change']) }}%
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-info-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">متوسط قيمة الطلب</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($salesReport['average_order_value'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-warning-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي الضرائب</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($salesReport['total_tax'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">إجمالي الشحن</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($salesReport['total_shipping'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">إجمالي الخصومات</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($salesReport['total_discount'], 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">من</h6>
                            <h4 class="fw-bold mb-0">{{ $salesReport['start_date'] }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">إلى</h6>
                            <h4 class="fw-bold mb-0">{{ $salesReport['end_date'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <!-- Sales Chart -->
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">مبيعات {{ $period === 'today' ? 'اليوم' : ($period === 'week' ? 'هذا الأسبوع' : ($period === 'month' ? 'هذا الشهر' : 'هذه السنة')) }}</h5>
                        </div>
                        <div class="card-body">
                            <div id="sales-chart" style="min-height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Chart -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المبيعات الشهرية (آخر 12 شهر)</h5>
                        </div>
                        <div class="card-body">
                            <div id="monthly-sales-chart" style="min-height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
    <script>
        // Sales Chart
        var salesChartOptions = {
            series: [{
                name: 'الإيرادات',
                data: @json($chartData['revenue'])
            }, {
                name: 'عدد الطلبات',
                data: @json($chartData['orders'])
            }],
            chart: {
                type: 'area',
                height: 400,
                toolbar: {
                    show: true
                },
                fontFamily: 'Nunito, sans-serif',
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: @json($chartData['labels']),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: [{
                title: {
                    text: 'الإيرادات (ر.س)'
                }
            }, {
                opposite: true,
                title: {
                    text: 'عدد الطلبات'
                }
            }],
            colors: ['#0162e8', '#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, { seriesIndex }) {
                        if (seriesIndex === 0) {
                            return val.toFixed(2) + ' ر.س';
                        }
                        return val + ' طلب';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        var salesChart = new ApexCharts(document.querySelector("#sales-chart"), salesChartOptions);
        salesChart.render();

        // Monthly Sales Chart
        var monthlySalesChartOptions = {
            series: [{
                name: 'الإيرادات',
                data: @json($monthlySales['revenue'])
            }],
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: true
                },
                fontFamily: 'Nunito, sans-serif',
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '55%',
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: @json($monthlySales['labels']),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'الإيرادات (ر.س)'
                }
            },
            fill: {
                opacity: 1,
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.3,
                    gradientToColors: ['#0162e8'],
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 0.8,
                    stops: [0, 100]
                }
            },
            colors: ['#0162e8'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toFixed(2) + ' ر.س';
                    }
                }
            }
        };

        var monthlySalesChart = new ApexCharts(document.querySelector("#monthly-sales-chart"), monthlySalesChartOptions);
        monthlySalesChart.render();
    </script>
@stop

