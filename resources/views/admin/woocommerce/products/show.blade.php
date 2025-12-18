@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المنتج
@stop

@section('css')
<style>
    .product-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
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
                    <h5 class="page-title fs-21 mb-1">تفاصيل المنتج</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    @if($product->woo_id)
                        <a href="{{ route('woocommerce.products.edit', $product->id) }}" class="btn btn-primary">
                            <i class="fe fe-edit"></i> تعديل
                        </a>
                        <form action="{{ route('woocommerce.products.destroy', $product->id) }}" method="POST" 
                              class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟ سيتم حذفه من WooCommerce أيضاً.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fe fe-trash-2"></i> حذف
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('woocommerce.products.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <!-- الصور والمعلومات الأساسية -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            @if($product->images && count($product->images) > 0)
                                <div class="mb-3">
                                    <img src="{{ $product->images[0]['src'] ?? $product->main_image }}" 
                                         alt="{{ $product->name }}" 
                                         class="product-image w-100 mb-3">
                                    
                                    @if(count($product->images) > 1)
                                        <div class="row g-2">
                                            @foreach(array_slice($product->images, 0, 4) as $image)
                                                <div class="col-3">
                                                    <img src="{{ $image['src'] ?? '' }}" 
                                                         alt="{{ $image['alt'] ?? '' }}" 
                                                         class="img-thumbnail" 
                                                         style="cursor: pointer;"
                                                         onclick="changeMainImage(this.src)">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="fe fe-image fs-48 text-muted"></i>
                                    <p class="text-muted mt-2">لا توجد صور</p>
                                </div>
                            @endif

                            <div class="mt-3">
                                <h4 class="mb-3">{{ $product->name }}</h4>
                                
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    @if($product->featured)
                                        <span class="badge bg-warning text-dark">
                                            <i class="fe fe-star"></i> منتج مميز
                                        </span>
                                    @endif
                                    @if($product->on_sale)
                                        <span class="badge bg-danger">عرض</span>
                                    @endif
                                </div>

                                <div class="info-card p-3 bg-light rounded mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">السعر العادي:</span>
                                        <strong class="text-decoration-line-through text-muted">
                                            {{ number_format($product->regular_price ?? 0, 2) }} ر.س
                                        </strong>
                                    </div>
                                    @if($product->sale_price)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">سعر التخفيض:</span>
                                            <strong class="text-danger fs-18">
                                                {{ number_format($product->sale_price, 2) }} ر.س
                                            </strong>
                                        </div>
                                    @else
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">السعر:</span>
                                            <strong class="text-primary fs-18">
                                                {{ number_format($product->price ?? $product->regular_price ?? 0, 2) }} ر.س
                                            </strong>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-grid gap-2">
                                    <span class="badge bg-{{ $product->status === 'publish' ? 'success' : ($product->status === 'draft' ? 'warning' : 'secondary') }} fs-14 p-2">
                                        الحالة: {{ $product->status === 'publish' ? 'منشور' : ($product->status === 'draft' ? 'مسودة' : $product->status) }}
                                    </span>
                                    <span class="badge bg-{{ $product->stock_status === 'instock' ? 'success' : 'danger' }} fs-14 p-2">
                                        المخزون: {{ $product->stock_status === 'instock' ? 'متوفر' : ($product->stock_status === 'outofstock' ? 'نفد' : 'قيد الطلب') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- التفاصيل الكاملة -->
                <div class="col-xl-8 col-lg-7">
                    <!-- المعلومات الأساسية -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">المعلومات الأساسية</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">اسم المنتج</label>
                                    <p class="mb-0 fw-bold">{{ $product->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">نوع المنتج</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">
                                            {{ $product->type === 'simple' ? 'بسيط' : ($product->type === 'variable' ? 'متغير' : ($product->type === 'grouped' ? 'مجموعة' : 'خارجي')) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">رمز المنتج (SKU)</label>
                                    <p class="mb-0">{{ $product->sku ?: '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">رابط المنتج (Slug)</label>
                                    <p class="mb-0"><code>{{ $product->slug ?: '-' }}</code></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">رؤية الكتالوج</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary">
                                            {{ $product->catalog_visibility === 'visible' ? 'ظاهر' : ($product->catalog_visibility === 'hidden' ? 'مخفي' : $product->catalog_visibility) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">إجمالي المبيعات</label>
                                    <p class="mb-0 fw-bold text-success">{{ $product->total_sales ?? 0 }} عملية بيع</p>
                                </div>
                                @if($product->short_description)
                                    <div class="col-12">
                                        <label class="text-muted small">الوصف المختصر</label>
                                        <p class="mb-0">{{ $product->short_description }}</p>
                                    </div>
                                @endif
                                @if($product->description)
                                    <div class="col-12">
                                        <label class="text-muted small">الوصف الكامل</label>
                                        <div class="mb-0">{!! $product->description !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- السعر والمخزون -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">السعر والمخزون</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small">السعر العادي</label>
                                    <p class="mb-0 fw-bold">{{ number_format($product->regular_price ?? 0, 2) }} ر.س</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">سعر التخفيض</label>
                                    <p class="mb-0 fw-bold text-danger">
                                        {{ $product->sale_price ? number_format($product->sale_price, 2) . ' ر.س' : '-' }}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">السعر الحالي</label>
                                    <p class="mb-0 fw-bold text-primary">
                                        {{ number_format($product->price ?? $product->regular_price ?? 0, 2) }} ر.س
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">حالة المخزون</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $product->stock_status === 'instock' ? 'success' : 'danger' }}">
                                            {{ $product->stock_status === 'instock' ? 'متوفر' : ($product->stock_status === 'outofstock' ? 'نفد' : 'قيد الطلب') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">إدارة المخزون</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $product->manage_stock ? 'info' : 'secondary' }}">
                                            {{ $product->manage_stock ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                @if($product->manage_stock && $product->stock_quantity !== null)
                                    <div class="col-md-4">
                                        <label class="text-muted small">الكمية المتاحة</label>
                                        <p class="mb-0 fw-bold">{{ $product->stock_quantity }}</p>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <label class="text-muted small">السماح بالطلبات المسبقة</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary">{{ $product->backorders ?? 'لا' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الضرائب -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">الضرائب</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small">حالة الضريبة</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">
                                            {{ $product->tax_status === 'taxable' ? 'خاضع للضريبة' : ($product->tax_status === 'shipping' ? 'ضريبة الشحن فقط' : 'غير خاضع للضريبة') }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">فئة الضريبة</label>
                                    <p class="mb-0">{{ $product->tax_class ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الوزن والأبعاد -->
                    @if($product->weight || ($product->dimensions && (($product->dimensions['length'] ?? null) || ($product->dimensions['width'] ?? null) || ($product->dimensions['height'] ?? null))))
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">الوزن والأبعاد</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if($product->weight)
                                        <div class="col-md-3">
                                            <label class="text-muted small">الوزن</label>
                                            <p class="mb-0 fw-bold">{{ $product->weight }} كجم</p>
                                        </div>
                                    @endif
                                    @if($product->dimensions)
                                        @if($product->dimensions['length'] ?? null)
                                            <div class="col-md-3">
                                                <label class="text-muted small">الطول</label>
                                                <p class="mb-0">{{ $product->dimensions['length'] }} سم</p>
                                            </div>
                                        @endif
                                        @if($product->dimensions['width'] ?? null)
                                            <div class="col-md-3">
                                                <label class="text-muted small">العرض</label>
                                                <p class="mb-0">{{ $product->dimensions['width'] }} سم</p>
                                            </div>
                                        @endif
                                        @if($product->dimensions['height'] ?? null)
                                            <div class="col-md-3">
                                                <label class="text-muted small">الارتفاع</label>
                                                <p class="mb-0">{{ $product->dimensions['height'] }} سم</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- الفئات والعلامات -->
                    @if(($product->categories && count($product->categories) > 0) || ($product->tags && count($product->tags) > 0))
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">الفئات والعلامات</h6>
                            </div>
                            <div class="card-body">
                                @if($product->categories && count($product->categories) > 0)
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-2">الفئات</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->categories as $category)
                                                <span class="badge bg-primary">
                                                    {{ $category['name'] ?? 'غير محدد' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if($product->tags && count($product->tags) > 0)
                                    <div>
                                        <label class="text-muted small d-block mb-2">العلامات</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->tags as $tag)
                                                <span class="badge bg-secondary">
                                                    {{ $tag['name'] ?? 'غير محدد' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- الخصائص الإضافية -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">الخصائص الإضافية</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="text-muted small">منتج افتراضي</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $product->virtual ? 'success' : 'secondary' }}">
                                            {{ $product->virtual ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">قابل للتحميل</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $product->downloadable ? 'success' : 'secondary' }}">
                                            {{ $product->downloadable ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small">قابل للشراء</label>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $product->purchasable ? 'success' : 'secondary' }}">
                                            {{ $product->purchasable ? 'نعم' : 'لا' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
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
                                    <p class="mb-0"><code>#{{ $product->woo_id }}</code></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">تاريخ الإنشاء</label>
                                    <p class="mb-0">{{ $product->woo_created_at?->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">آخر تحديث</label>
                                    <p class="mb-0">{{ $product->woo_updated_at?->format('Y-m-d H:i:s') ?? '-' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small">تاريخ المزامنة المحلية</label>
                                    <p class="mb-0">{{ $product->updated_at->format('Y-m-d H:i:s') }}</p>
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
<script>
    function changeMainImage(src) {
        document.querySelector('.product-image').src = src;
    }
</script>
@stop

