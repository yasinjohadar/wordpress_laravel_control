@extends('admin.layouts.master')

@section('page-title')
    تعديل العميل
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
                    <h5 class="page-title fs-21 mb-1">تعديل العميل</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.customers.show', $customer->id) }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للتفاصيل
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('woocommerce.customers.update', $customer->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- المعلومات الأساسية -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">المعلومات الأساسية</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                                   name="first_name" placeholder="الاسم الأول" 
                                                   value="{{ old('first_name', $customer->first_name) }}" required>
                                            <label>الاسم الأول <span class="text-danger">*</span></label>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                                   name="last_name" placeholder="الاسم الأخير" 
                                                   value="{{ old('last_name', $customer->last_name) }}" required>
                                            <label>الاسم الأخير <span class="text-danger">*</span></label>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   name="email" placeholder="البريد الإلكتروني" 
                                                   value="{{ old('email', $customer->email) }}" required>
                                            <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                                   name="username" placeholder="اسم المستخدم" 
                                                   value="{{ old('username', $customer->username) }}" readonly>
                                            <label>اسم المستخدم <span class="text-muted small">(غير قابل للتعديل)</span></label>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">اسم المستخدم غير قابل للتعديل في WooCommerce</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   name="password" placeholder="كلمة المرور (اتركه فارغاً للاحتفاظ بالكلمة الحالية)">
                                            <label>كلمة المرور (اختياري)</label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- عنوان الفواتير -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">عنوان الفواتير</h6>
                                    </div>

                                    @php
                                        $billing = $customer->billing_address ?? [];
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.first_name') is-invalid @enderror" 
                                                   name="billing[first_name]" placeholder="الاسم الأول" 
                                                   value="{{ old('billing.first_name', $billing['first_name'] ?? '') }}">
                                            <label>الاسم الأول</label>
                                            @error('billing.first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.last_name') is-invalid @enderror" 
                                                   name="billing[last_name]" placeholder="الاسم الأخير" 
                                                   value="{{ old('billing.last_name', $billing['last_name'] ?? '') }}">
                                            <label>الاسم الأخير</label>
                                            @error('billing.last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.company') is-invalid @enderror" 
                                                   name="billing[company]" placeholder="اسم الشركة" 
                                                   value="{{ old('billing.company', $billing['company'] ?? '') }}">
                                            <label>اسم الشركة</label>
                                            @error('billing.company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.phone') is-invalid @enderror" 
                                                   name="billing[phone]" placeholder="رقم الهاتف" 
                                                   value="{{ old('billing.phone', $billing['phone'] ?? '') }}">
                                            <label>رقم الهاتف</label>
                                            @error('billing.phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.address_1') is-invalid @enderror" 
                                                   name="billing[address_1]" placeholder="العنوان الأول" 
                                                   value="{{ old('billing.address_1', $billing['address_1'] ?? '') }}">
                                            <label>العنوان الأول</label>
                                            @error('billing.address_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.address_2') is-invalid @enderror" 
                                                   name="billing[address_2]" placeholder="العنوان الثاني" 
                                                   value="{{ old('billing.address_2', $billing['address_2'] ?? '') }}">
                                            <label>العنوان الثاني</label>
                                            @error('billing.address_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.city') is-invalid @enderror" 
                                                   name="billing[city]" placeholder="المدينة" 
                                                   value="{{ old('billing.city', $billing['city'] ?? '') }}">
                                            <label>المدينة</label>
                                            @error('billing.city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.state') is-invalid @enderror" 
                                                   name="billing[state]" placeholder="المنطقة" 
                                                   value="{{ old('billing.state', $billing['state'] ?? '') }}">
                                            <label>المنطقة</label>
                                            @error('billing.state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.postcode') is-invalid @enderror" 
                                                   name="billing[postcode]" placeholder="الرمز البريدي" 
                                                   value="{{ old('billing.postcode', $billing['postcode'] ?? '') }}">
                                            <label>الرمز البريدي</label>
                                            @error('billing.postcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('billing.country') is-invalid @enderror" 
                                                   name="billing[country]" placeholder="الدولة" 
                                                   value="{{ old('billing.country', $billing['country'] ?? 'SA') }}">
                                            <label>الدولة</label>
                                            @error('billing.country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('billing.email') is-invalid @enderror" 
                                                   name="billing[email]" placeholder="البريد الإلكتروني" 
                                                   value="{{ old('billing.email', $billing['email'] ?? '') }}">
                                            <label>البريد الإلكتروني</label>
                                            @error('billing.email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- عنوان الشحن -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">عنوان الشحن</h6>
                                    </div>

                                    @php
                                        $shipping = $customer->shipping_address ?? [];
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.first_name') is-invalid @enderror" 
                                                   name="shipping[first_name]" placeholder="الاسم الأول" 
                                                   value="{{ old('shipping.first_name', $shipping['first_name'] ?? '') }}">
                                            <label>الاسم الأول</label>
                                            @error('shipping.first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.last_name') is-invalid @enderror" 
                                                   name="shipping[last_name]" placeholder="الاسم الأخير" 
                                                   value="{{ old('shipping.last_name', $shipping['last_name'] ?? '') }}">
                                            <label>الاسم الأخير</label>
                                            @error('shipping.last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.company') is-invalid @enderror" 
                                                   name="shipping[company]" placeholder="اسم الشركة" 
                                                   value="{{ old('shipping.company', $shipping['company'] ?? '') }}">
                                            <label>اسم الشركة</label>
                                            @error('shipping.company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.address_1') is-invalid @enderror" 
                                                   name="shipping[address_1]" placeholder="العنوان الأول" 
                                                   value="{{ old('shipping.address_1', $shipping['address_1'] ?? '') }}">
                                            <label>العنوان الأول</label>
                                            @error('shipping.address_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.address_2') is-invalid @enderror" 
                                                   name="shipping[address_2]" placeholder="العنوان الثاني" 
                                                   value="{{ old('shipping.address_2', $shipping['address_2'] ?? '') }}">
                                            <label>العنوان الثاني</label>
                                            @error('shipping.address_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.city') is-invalid @enderror" 
                                                   name="shipping[city]" placeholder="المدينة" 
                                                   value="{{ old('shipping.city', $shipping['city'] ?? '') }}">
                                            <label>المدينة</label>
                                            @error('shipping.city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.state') is-invalid @enderror" 
                                                   name="shipping[state]" placeholder="المنطقة" 
                                                   value="{{ old('shipping.state', $shipping['state'] ?? '') }}">
                                            <label>المنطقة</label>
                                            @error('shipping.state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.postcode') is-invalid @enderror" 
                                                   name="shipping[postcode]" placeholder="الرمز البريدي" 
                                                   value="{{ old('shipping.postcode', $shipping['postcode'] ?? '') }}">
                                            <label>الرمز البريدي</label>
                                            @error('shipping.postcode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('shipping.country') is-invalid @enderror" 
                                                   name="shipping[country]" placeholder="الدولة" 
                                                   value="{{ old('shipping.country', $shipping['country'] ?? 'SA') }}">
                                            <label>الدولة</label>
                                            @error('shipping.country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- أزرار الحفظ -->
                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('woocommerce.customers.show', $customer->id) }}" class="btn btn-secondary">
                                                إلغاء
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-save"></i> تحديث العميل
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
@stop

