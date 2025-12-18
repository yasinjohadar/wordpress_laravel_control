@extends('admin.layouts.master')

@section('page-title')
    لوحة تحكم المتجر
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">لوحة تحكم المتجر</h5>
                </div>
                <div class="ms-auto">
                    <form action="{{ route('woocommerce.sync') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-refresh-cw"></i> مزامنة الكل
                        </button>
                    </form>
                </div>
            </div>
            <!-- End Page Header -->

            @if(isset($connectionStatus))
                <div class="alert alert-{{ $connectionStatus['connected'] ? 'success' : 'danger' }} alert-dismissible fade show">
                    <strong>حالة الاتصال:</strong> {{ $connectionStatus['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-primary-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي الطلبات</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ $stats['orders'] ?? 0 }}</h4>
                            <small class="text-fixed-white">اليوم: {{ $stats['today_orders'] ?? 0 }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-success-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">مبيعات اليوم</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($todaySales['total_revenue'] ?? 0, 2) }} ر.س</h4>
                            <small class="text-fixed-white">{{ $todaySales['total_orders'] ?? 0 }} طلب</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-info-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">مبيعات الشهر</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($monthSales['total_revenue'] ?? 0, 2) }} ر.س</h4>
                            <small class="text-fixed-white">{{ $monthSales['total_orders'] ?? 0 }} طلب</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-warning-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">متوسط قيمة الطلب</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ number_format($monthSales['average_order_value'] ?? 0, 2) }} ر.س</h4>
                            <small class="text-fixed-white">هذا الشهر</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">إجمالي المنتجات</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['products'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">العملاء</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['customers'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">الكوبونات</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['coupons'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-12 mb-2 text-muted">إجمالي الخصومات</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($monthSales['total_discount'] ?? 0, 2) }} ر.س</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Recent Orders -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">أحدث الطلبات</h5>
                            <a href="{{ route('woocommerce.orders.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>الحالة</th>
                                        <th>الإجمالي</th>
                                        <th>التاريخ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('woocommerce.orders.show', $order->id) }}">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'primary' : 'warning') }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td>{{ $order->formatted_total }}</td>
                                            <td>{{ $order->woo_created_at?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">المنتجات الأكثر مبيعاً</h5>
                            <a href="{{ route('woocommerce.reports.products') }}" class="btn btn-sm btn-success">عرض الكل</a>
                        </div>
                        <div class="card-body">
                            @forelse($topProducts as $product)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('woocommerce.products.show', $product['id']) }}">
                                                {{ \Illuminate\Support\Str::limit($product['name'], 30) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $product['sales'] }} مبيعات</small>
                                    </div>
                                    <div class="text-end">
                                        <strong>{{ number_format($product['revenue'], 2) }} ر.س</strong>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted mb-0">لا توجد بيانات</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
@stop


