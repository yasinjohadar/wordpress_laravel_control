@extends('admin.layouts.master')

@section('page-title')
    قائمة الكوبونات
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
                    <h5 class="page-title fs-21 mb-1">كافة الكوبونات</h5>
                </div>
                <div class="ms-auto">
                    <form action="{{ route('woocommerce.coupons.sync') }}" method="POST" class="d-inline">
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
                                <form action="{{ route('woocommerce.coupons.index') }}" method="GET"
                                      class="d-flex align-items-center gap-2">
                                    <input style="width: 260px" type="text" name="search" class="form-control"
                                           placeholder="بحث بكود الكوبون" value="{{ request('search') }}">

                                    <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                                    <a href="{{ route('woocommerce.coupons.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>الكود</th>
                                        <th>نوع الخصم</th>
                                        <th>القيمة</th>
                                        <th>مرات الاستخدام</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($coupons as $coupon)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><span class="badge bg-dark text-uppercase">{{ $coupon->code }}</span></td>
                                            <td>{{ $coupon->discount_type }}</td>
                                            <td>{{ $coupon->amount }}</td>
                                            <td>{{ $coupon->usage_count }}</td>
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
                                {{ $coupons->withQueryString()->links() }}
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


