@extends('admin.layouts.master')

@section('page-title')
    تقرير المنتجات
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير المنتجات</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.reports.index') }}" class="btn btn-primary">
                        <i class="fe fe-arrow-right"></i> العودة للتقارير
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Top Selling Products -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">المنتجات الأكثر مبيعاً</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>عدد المبيعات</th>
                                        <th>السعر</th>
                                        <th>إجمالي الإيرادات</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($topProducts as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $product['name'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $product['sales'] }}</span>
                                            </td>
                                            <td>{{ number_format($product['price'], 2) }} ر.س</td>
                                            <td>
                                                <strong class="text-success">{{ number_format($product['revenue'], 2) }} ر.س</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('woocommerce.products.show', $product['id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fe fe-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Performance -->
            <div class="row">
                <!-- High Sales Products -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">منتجات عالية المبيعات</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>المبيعات</th>
                                        <th>الإيرادات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($productsPerformance['high_sales'] as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('woocommerce.products.show', $product['id']) }}">
                                                    {{ \Illuminate\Support\Str::limit($product['name'], 40) }}
                                                </a>
                                            </td>
                                            <td>{{ $product['sales'] }}</td>
                                            <td>{{ number_format($product['revenue'], 2) }} ر.س</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Sales Products -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">منتجات قليلة المبيعات</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>المبيعات</th>
                                        <th>المخزون</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($productsPerformance['low_sales'] as $product)
                                        <tr>
                                            <td>
                                                <a href="{{ route('woocommerce.products.show', $product['id']) }}">
                                                    {{ \Illuminate\Support\Str::limit($product['name'], 40) }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $product['sales'] }}</span>
                                            </td>
                                            <td>{{ $product['stock'] ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
@stop

