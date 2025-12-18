@extends('admin.layouts.master')

@section('page-title')
    قائمة الطلبات
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
                    <h5 class="page-title fs-21 mb-1">كافة الطلبات</h5>
                </div>
                <div class="ms-auto">
                    <form action="{{ route('woocommerce.orders.sync') }}" method="POST" class="d-inline">
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
                        <div class="card-header">
                            <div class="row g-3">
                                <div class="col-12">
                                    <form action="{{ route('woocommerce.orders.index') }}" method="GET" class="row g-2">
                                        <div class="col-md-2">
                                            <input type="text" name="search" class="form-control"
                                                   placeholder="بحث برقم الطلب" value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="status" class="form-select">
                                                <option value="">كل الحالات</option>
                                                <option value="pending" @selected(request('status') === 'pending')>قيد الانتظار</option>
                                                <option value="processing" @selected(request('status') === 'processing')>قيد المعالجة</option>
                                                <option value="on-hold" @selected(request('status') === 'on-hold')>معلق</option>
                                                <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                                                <option value="cancelled" @selected(request('status') === 'cancelled')>ملغي</option>
                                                <option value="refunded" @selected(request('status') === 'refunded')>مسترد</option>
                                                <option value="failed" @selected(request('status') === 'failed')>فاشل</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" name="date_from" class="form-control"
                                                   placeholder="من تاريخ" value="{{ request('date_from') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" name="date_to" class="form-control"
                                                   placeholder="إلى تاريخ" value="{{ request('date_to') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="min_amount" class="form-control"
                                                   placeholder="الحد الأدنى" step="0.01" value="{{ request('min_amount') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="max_amount" class="form-control"
                                                   placeholder="الحد الأقصى" step="0.01" value="{{ request('max_amount') }}">
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fe fe-search"></i> بحث
                                            </button>
                                            <a href="{{ route('woocommerce.orders.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="fe fe-x"></i> مسح
                                            </a>
                                            <a href="{{ route('woocommerce.orders.export', request()->all()) }}" class="btn btn-success btn-sm">
                                                <i class="fe fe-download"></i> تصدير CSV
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>رقم الطلب</th>
                                        <th>العميل</th>
                                        <th>الإجمالي</th>
                                        <th>الحالة</th>
                                        <th>طريقة الدفع</th>
                                        <th>التاريخ</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.orders.show', $order->id) }}">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($order->customer)
                                                    <a href="{{ route('woocommerce.customers.show', $order->customer->id) }}">
                                                        {{ $order->customer->full_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $order->formatted_total }}</td>
                                            <td>
                                                <span class="badge bg-{{ match($order->status) {
                                                    'completed' => 'success',
                                                    'processing' => 'primary',
                                                    'pending' => 'warning',
                                                    'cancelled' => 'danger',
                                                    'refunded' => 'info',
                                                    'on-hold' => 'secondary',
                                                    default => 'dark'
                                                } }}">
                                                    {{ match($order->status) {
                                                        'pending' => 'قيد الانتظار',
                                                        'processing' => 'قيد المعالجة',
                                                        'on-hold' => 'معلق',
                                                        'completed' => 'مكتمل',
                                                        'cancelled' => 'ملغي',
                                                        'refunded' => 'مسترد',
                                                        'failed' => 'فاشل',
                                                        default => $order->status
                                                    } }}
                                                </span>
                                            </td>
                                            <td>{{ $order->payment_method_title ?? 'N/A' }}</td>
                                            <td>{{ $order->woo_created_at?->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fe fe-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $orders->withQueryString()->links() }}
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


