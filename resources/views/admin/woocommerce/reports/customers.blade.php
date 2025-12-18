@extends('admin.layouts.master')

@section('page-title')
    تقرير العملاء
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير العملاء</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.reports.index') }}" class="btn btn-primary">
                        <i class="fe fe-arrow-right"></i> العودة للتقارير
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Top Customers -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">العملاء الأكثر شراءً</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم العميل</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>عدد الطلبات</th>
                                        <th>إجمالي المشتريات</th>
                                        <th>متوسط قيمة الطلب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($topCustomers as $index => $customer)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $customer['name'] }}</strong>
                                            </td>
                                            <td>{{ $customer['email'] }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $customer['orders_count'] }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ number_format($customer['total_spent'], 2) }} ر.س</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $avgOrder = $customer['orders_count'] > 0 
                                                        ? $customer['total_spent'] / $customer['orders_count'] 
                                                        : 0;
                                                @endphp
                                                {{ number_format($avgOrder, 2) }} ر.س
                                            </td>
                                            <td>
                                                <a href="{{ route('woocommerce.customers.show', $customer['id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fe fe-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-primary-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي العملاء</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">{{ count($topCustomers) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-success-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي المشتريات</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">
                                {{ number_format(collect($topCustomers)->sum('total_spent'), 2) }} ر.س
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-info-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">إجمالي الطلبات</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">
                                {{ collect($topCustomers)->sum('orders_count') }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="card bg-warning-gradient">
                        <div class="card-body">
                            <h6 class="text-fixed-white fs-12 mb-2">متوسط قيمة الطلب</h6>
                            <h4 class="text-fixed-white fw-bold mb-0">
                                @php
                                    $totalOrders = collect($topCustomers)->sum('orders_count');
                                    $totalSpent = collect($topCustomers)->sum('total_spent');
                                    $avg = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
                                @endphp
                                {{ number_format($avg, 2) }} ر.س
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
@stop

