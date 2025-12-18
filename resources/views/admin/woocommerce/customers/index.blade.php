@extends('admin.layouts.master')

@section('page-title')
    قائمة العملاء
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
                    <h5 class="page-title fs-21 mb-1">كافة العملاء</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('woocommerce.customers.create') }}" class="btn btn-success">
                        <i class="fe fe-plus"></i> إنشاء عميل جديد
                    </a>
                    <form action="{{ route('woocommerce.customers.sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-refresh-cw"></i> مزامنة من WooCommerce
                        </button>
                    </form>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            <div class="flex-shrink-0">
                                <form action="{{ route('woocommerce.customers.index') }}" method="GET"
                                      class="d-flex align-items-center gap-2">
                                    <input style="width: 260px" type="text" name="search" class="form-control"
                                           placeholder="بحث بالاسم أو البريد" value="{{ request('search') }}">

                                    <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                                    <a href="{{ route('woocommerce.customers.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>الاسم</th>
                                        <th>البريد</th>
                                        <th>إجمالي الطلبات</th>
                                        <th>إجمالي المشتريات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.customers.show', $customer->id) }}" 
                                                   class="text-primary text-decoration-none">
                                                    {{ $customer->full_name }}
                                                </a>
                                            </td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->orders_count }}</td>
                                            <td>{{ number_format($customer->total_spent ?? 0, 2) }} ر.س</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $customers->withQueryString()->links() }}
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


