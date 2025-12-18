@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الطلب #{{ $order->order_number }}
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
                    <h5 class="page-title fs-21 mb-1">تفاصيل الطلب #{{ $order->order_number }}</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.orders.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للقائمة
                    </a>
                    <a href="{{ route('woocommerce.orders.invoice', $order->id) }}" target="_blank" class="btn btn-primary">
                        <i class="fe fe-printer"></i> طباعة الفاتورة
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <!-- Order Details -->
                <div class="col-xl-8">
                    <!-- Order Status Update -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">تحديث حالة الطلب</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('woocommerce.orders.update-status', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-8">
                                        <select name="status" class="form-select" required>
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                            <option value="on-hold" {{ $order->status === 'on-hold' ? 'selected' : '' }}>معلق</option>
                                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>مسترد</option>
                                            <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>فاشل</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fe fe-save"></i> تحديث الحالة
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">عناصر الطلب</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>السعر</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $lineItems = is_array($order->line_items) ? $order->line_items : [];
                                    @endphp
                                    @forelse($lineItems as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item['name'] ?? 'N/A' }}</strong>
                                                @if(isset($item['sku']))
                                                    <br><small class="text-muted">SKU: {{ $item['sku'] }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item['quantity'] ?? 0 }}</td>
                                            <td>{{ number_format($item['price'] ?? 0, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                                            <td>{{ number_format(($item['quantity'] ?? 0) * ($item['price'] ?? 0), 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">لا توجد عناصر</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Add Note -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">إضافة ملاحظة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('woocommerce.orders.add-note', $order->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="note" class="form-control" rows="3" placeholder="اكتب ملاحظة..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="customer_note" value="1" id="customer_note">
                                        <label class="form-check-label" for="customer_note">
                                            إرسال الملاحظة للعميل
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe fe-plus"></i> إضافة ملاحظة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-xl-4">
                    <!-- Order Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الطلب</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>الحالة:</strong>
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
                            </div>
                            <div class="mb-3">
                                <strong>تاريخ الطلب:</strong><br>
                                {{ $order->woo_created_at?->format('Y-m-d H:i:s') }}
                            </div>
                            @if($order->date_paid)
                                <div class="mb-3">
                                    <strong>تاريخ الدفع:</strong><br>
                                    {{ $order->date_paid->format('Y-m-d H:i:s') }}
                                </div>
                            @endif
                            @if($order->date_completed)
                                <div class="mb-3">
                                    <strong>تاريخ الإكمال:</strong><br>
                                    {{ $order->date_completed->format('Y-m-d H:i:s') }}
                                </div>
                            @endif
                            <div class="mb-3">
                                <strong>طريقة الدفع:</strong><br>
                                {{ $order->payment_method_title ?? 'N/A' }}
                            </div>
                            @if($order->transaction_id)
                                <div class="mb-3">
                                    <strong>رقم المعاملة:</strong><br>
                                    {{ $order->transaction_id }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات العميل</h5>
                        </div>
                        <div class="card-body">
                            @if($order->customer)
                                <div class="mb-3">
                                    <strong>الاسم:</strong><br>
                                    <a href="{{ route('woocommerce.customers.show', $order->customer->id) }}">
                                        {{ $order->customer->full_name }}
                                    </a>
                                </div>
                                <div class="mb-3">
                                    <strong>البريد الإلكتروني:</strong><br>
                                    {{ $order->customer->email }}
                                </div>
                            @else
                                <p class="text-muted">لا توجد معلومات عميل</p>
                            @endif
                        </div>
                    </div>

                    <!-- Billing Address -->
                    @php
                        $billing = is_array($order->billing_address) ? $order->billing_address : [];
                    @endphp
                    @if(!empty($billing))
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">عنوان الفواتير</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-1">
                                    {{ $billing['first_name'] ?? '' }} {{ $billing['last_name'] ?? '' }}<br>
                                    {{ $billing['company'] ?? '' }}<br>
                                    {{ $billing['address_1'] ?? '' }}<br>
                                    @if(isset($billing['address_2']))
                                        {{ $billing['address_2'] }}<br>
                                    @endif
                                    {{ $billing['city'] ?? '' }}, {{ $billing['state'] ?? '' }} {{ $billing['postcode'] ?? '' }}<br>
                                    {{ $billing['country'] ?? '' }}<br>
                                    @if(isset($billing['phone']))
                                        <strong>الهاتف:</strong> {{ $billing['phone'] }}<br>
                                    @endif
                                    @if(isset($billing['email']))
                                        <strong>البريد:</strong> {{ $billing['email'] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Order Totals -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">ملخص الطلب</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>المجموع الفرعي:</span>
                                <strong>{{ number_format($order->subtotal, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</strong>
                            </div>
                            @if($order->discount_total > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>الخصم:</span>
                                    <strong>-{{ number_format($order->discount_total, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</strong>
                                </div>
                            @endif
                            @if($order->shipping_total > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>الشحن:</span>
                                    <strong>{{ number_format($order->shipping_total, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</strong>
                                </div>
                            @endif
                            @if($order->total_tax > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>الضريبة:</span>
                                    <strong>{{ number_format($order->total_tax, 2) }} {{ $order->currency_symbol ?? 'ر.س' }}</strong>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>الإجمالي:</strong>
                                <strong class="text-primary fs-18">{{ $order->formatted_total }}</strong>
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

