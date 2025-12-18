@extends('admin.layouts.master')

@section('page-title')
    تفاصيل العميل
@stop

@section('css')
<style>
    .info-card {
        border-left: 3px solid #007bff;
    }
</style>
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
                    <h5 class="page-title fs-21 mb-1">تفاصيل العميل</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    @if($customer->woo_id)
                        <a href="{{ route('woocommerce.customers.edit', $customer->id) }}" class="btn btn-primary">
                            <i class="fe fe-edit"></i> تعديل
                        </a>
                        <form action="{{ route('woocommerce.customers.destroy', $customer->id) }}" method="POST" 
                              class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟ سيتم حذفه من WooCommerce أيضاً.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fe fe-trash-2"></i> حذف
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('woocommerce.customers.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <!-- المعلومات الأساسية -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                @if($customer->avatar_url)
                                    <img src="{{ $customer->avatar_url }}" alt="{{ $customer->full_name }}" 
                                         class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                                         style="width: 100px; height: 100px; font-size: 2.5rem;">
                                        {{ strtoupper(substr($customer->first_name ?? $customer->email, 0, 1)) }}
                                    </div>
                                @endif
                                <h4 class="mt-3 mb-1">{{ $customer->full_name }}</h4>
                                <p class="text-muted mb-0">{{ $customer->email }}</p>
                            </div>

                            <div class="info-card p-3 bg-light rounded mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">إجمالي الطلبات:</span>
                                    <strong class="text-primary">{{ $customer->orders_count ?? 0 }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">إجمالي المشتريات:</span>
                                    <strong class="text-success fs-18">
                                        {{ number_format($customer->total_spent ?? 0, 2) }} ر.س
                                    </strong>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <span class="badge bg-{{ $customer->is_paying_customer ? 'success' : 'secondary' }} fs-14 p-2">
                                    {{ $customer->is_paying_customer ? 'عميل دافع' : 'عميل عادي' }}
                                </span>
                                <span class="badge bg-info fs-14 p-2">
                                    الدور: {{ $customer->role ?? 'customer' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- التفاصيل الكاملة -->
                <div class="col-xl-8 col-lg-7">
                    <!-- المعلومات الشخصية -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">المعلومات الشخصية</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">الاسم الكامل</label>
                                    <p class="mb-0 fw-bold">{{ $customer->full_name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">البريد الإلكتروني</label>
                                    <p class="mb-0">{{ $customer->email }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">اسم المستخدم</label>
                                    <p class="mb-0">{{ $customer->username ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">الدور</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ $customer->role ?? 'customer' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- عنوان الفواتير -->
                    @if($customer->billing_address && count($customer->billing_address) > 0)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">عنوان الفواتير</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if(isset($customer->billing_address['first_name']) || isset($customer->billing_address['last_name']))
                                        <div class="col-12">
                                            <label class="text-muted small">الاسم</label>
                                            <p class="mb-0">
                                                {{ ($customer->billing_address['first_name'] ?? '') . ' ' . ($customer->billing_address['last_name'] ?? '') }}
                                            </p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['company']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">الشركة</label>
                                            <p class="mb-0">{{ $customer->billing_address['company'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['phone']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">رقم الهاتف</label>
                                            <p class="mb-0">{{ $customer->billing_address['phone'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['address_1']))
                                        <div class="col-12">
                                            <label class="text-muted small">العنوان</label>
                                            <p class="mb-0">
                                                {{ $customer->billing_address['address_1'] }}
                                                @if(isset($customer->billing_address['address_2']))
                                                    <br>{{ $customer->billing_address['address_2'] }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['city']) || isset($customer->billing_address['state']) || isset($customer->billing_address['postcode']))
                                        <div class="col-md-4">
                                            <label class="text-muted small">المدينة</label>
                                            <p class="mb-0">{{ $customer->billing_address['city'] ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">المنطقة</label>
                                            <p class="mb-0">{{ $customer->billing_address['state'] ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">الرمز البريدي</label>
                                            <p class="mb-0">{{ $customer->billing_address['postcode'] ?? '-' }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['country']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">الدولة</label>
                                            <p class="mb-0">{{ $customer->billing_address['country'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->billing_address['email']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">البريد الإلكتروني</label>
                                            <p class="mb-0">{{ $customer->billing_address['email'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- عنوان الشحن -->
                    @if($customer->shipping_address && count($customer->shipping_address) > 0)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">عنوان الشحن</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if(isset($customer->shipping_address['first_name']) || isset($customer->shipping_address['last_name']))
                                        <div class="col-12">
                                            <label class="text-muted small">الاسم</label>
                                            <p class="mb-0">
                                                {{ ($customer->shipping_address['first_name'] ?? '') . ' ' . ($customer->shipping_address['last_name'] ?? '') }}
                                            </p>
                                        </div>
                                    @endif
                                    @if(isset($customer->shipping_address['company']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">الشركة</label>
                                            <p class="mb-0">{{ $customer->shipping_address['company'] }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->shipping_address['address_1']))
                                        <div class="col-12">
                                            <label class="text-muted small">العنوان</label>
                                            <p class="mb-0">
                                                {{ $customer->shipping_address['address_1'] }}
                                                @if(isset($customer->shipping_address['address_2']))
                                                    <br>{{ $customer->shipping_address['address_2'] }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                    @if(isset($customer->shipping_address['city']) || isset($customer->shipping_address['state']) || isset($customer->shipping_address['postcode']))
                                        <div class="col-md-4">
                                            <label class="text-muted small">المدينة</label>
                                            <p class="mb-0">{{ $customer->shipping_address['city'] ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">المنطقة</label>
                                            <p class="mb-0">{{ $customer->shipping_address['state'] ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted small">الرمز البريدي</label>
                                            <p class="mb-0">{{ $customer->shipping_address['postcode'] ?? '-' }}</p>
                                        </div>
                                    @endif
                                    @if(isset($customer->shipping_address['country']))
                                        <div class="col-md-6">
                                            <label class="text-muted small">الدولة</label>
                                            <p class="mb-0">{{ $customer->shipping_address['country'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- طلبات العميل -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">طلبات العميل ({{ $orders->total() }})</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>الإجمالي</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($orders as $order)
                                        <tr>
                                            <td>#{{ $order->order_number }}</td>
                                            <td>{{ $order->woo_created_at?->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : 'warning') }}">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td>{{ $order->formatted_total }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.orders.show', $order->id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fe fe-eye"></i> عرض
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">لا توجد طلبات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if($orders->hasPages())
                                <div class="mt-3">
                                    {{ $orders->links() }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- معلومات المزامنة -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">معلومات المزامنة</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">معرف WooCommerce</label>
                                    <p class="mb-0"><code>#{{ $customer->woo_id }}</code></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">تاريخ الإنشاء</label>
                                    <p class="mb-0">{{ $customer->woo_created_at?->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">آخر تحديث</label>
                                    <p class="mb-0">{{ $customer->woo_updated_at?->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">تاريخ المزامنة المحلية</label>
                                    <p class="mb-0">{{ $customer->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
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

