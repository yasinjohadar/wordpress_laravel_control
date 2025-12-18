@extends('admin.layouts.master')

@section('page-title')
    تعديل المنتج
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
                    <h5 class="page-title fs-21 mb-1">تعديل المنتج</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.products.show', $product->id) }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للتفاصيل
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('woocommerce.products.update', $product->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <!-- المعلومات الأساسية -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">المعلومات الأساسية</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   name="name" placeholder="اسم المنتج" 
                                                   value="{{ old('name', $product->name) }}" required>
                                            <label>اسم المنتج <span class="text-danger">*</span></label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                                <option value="simple" @selected(old('type', $product->type) === 'simple')>بسيط</option>
                                                <option value="grouped" @selected(old('type', $product->type) === 'grouped')>مجموعة</option>
                                                <option value="external" @selected(old('type', $product->type) === 'external')>خارجي</option>
                                                <option value="variable" @selected(old('type', $product->type) === 'variable')>متغير</option>
                                            </select>
                                            <label>نوع المنتج <span class="text-danger">*</span></label>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                                <option value="draft" @selected(old('status', $product->status) === 'draft')>مسودة</option>
                                                <option value="pending" @selected(old('status', $product->status) === 'pending')>قيد المراجعة</option>
                                                <option value="publish" @selected(old('status', $product->status) === 'publish')>منشور</option>
                                            </select>
                                            <label>الحالة <span class="text-danger">*</span></label>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                                      name="short_description" placeholder="الوصف المختصر" 
                                                      style="height: 80px">{{ old('short_description', $product->short_description) }}</textarea>
                                            <label>الوصف المختصر</label>
                                            @error('short_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      name="description" placeholder="الوصف الكامل" 
                                                      style="height: 120px">{{ old('description', $product->description) }}</textarea>
                                            <label>الوصف الكامل</label>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- السعر والمخزون -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">السعر والمخزون</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('regular_price') is-invalid @enderror" 
                                                   name="regular_price" placeholder="السعر العادي" 
                                                   value="{{ old('regular_price', $product->regular_price) }}" required>
                                            <label>السعر العادي (ر.س) <span class="text-danger">*</span></label>
                                            @error('regular_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('sale_price') is-invalid @enderror" 
                                                   name="sale_price" placeholder="سعر التخفيض" 
                                                   value="{{ old('sale_price', $product->sale_price) }}">
                                            <label>سعر التخفيض (ر.س)</label>
                                            @error('sale_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                                   name="sku" placeholder="رمز المنتج (SKU)" 
                                                   value="{{ old('sku', $product->sku) }}">
                                            <label>رمز المنتج (SKU)</label>
                                            @error('sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <select class="form-select @error('stock_status') is-invalid @enderror" name="stock_status" required>
                                                <option value="instock" @selected(old('stock_status', $product->stock_status) === 'instock')>متوفر</option>
                                                <option value="outofstock" @selected(old('stock_status', $product->stock_status) === 'outofstock')>نفد المخزون</option>
                                                <option value="onbackorder" @selected(old('stock_status', $product->stock_status) === 'onbackorder')>قيد الطلب</option>
                                            </select>
                                            <label>حالة المخزون <span class="text-danger">*</span></label>
                                            @error('stock_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" name="manage_stock" 
                                                   id="manage_stock" value="1" 
                                                   @checked(old('manage_stock', $product->manage_stock)) 
                                                   onchange="toggleStockQuantity()">
                                            <label class="form-check-label" for="manage_stock">
                                                إدارة المخزون
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4" id="stock_quantity_field" 
                                         style="display: {{ old('manage_stock', $product->manage_stock) ? 'block' : 'none' }};">
                                        <div class="form-floating">
                                            <input type="number" min="0" 
                                                   class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                   name="stock_quantity" placeholder="الكمية" 
                                                   value="{{ old('stock_quantity', $product->stock_quantity) }}">
                                            <label>الكمية</label>
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- الضرائب -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">الضرائب</h6>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('tax_status') is-invalid @enderror" name="tax_status" required>
                                                <option value="taxable" @selected(old('tax_status', $product->tax_status) === 'taxable')>خاضع للضريبة</option>
                                                <option value="shipping" @selected(old('tax_status', $product->tax_status) === 'shipping')>ضريبة الشحن فقط</option>
                                                <option value="none" @selected(old('tax_status', $product->tax_status) === 'none')>غير خاضع للضريبة</option>
                                            </select>
                                            <label>حالة الضريبة <span class="text-danger">*</span></label>
                                            @error('tax_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('tax_class') is-invalid @enderror" 
                                                   name="tax_class" placeholder="فئة الضريبة" 
                                                   value="{{ old('tax_class', $product->tax_class) }}">
                                            <label>فئة الضريبة</label>
                                            @error('tax_class')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- الوزن والأبعاد -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">الوزن والأبعاد</h6>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('weight') is-invalid @enderror" 
                                                   name="weight" placeholder="الوزن (كجم)" 
                                                   value="{{ old('weight', $product->weight) }}">
                                            <label>الوزن (كجم)</label>
                                            @error('weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('length') is-invalid @enderror" 
                                                   name="length" placeholder="الطول (سم)" 
                                                   value="{{ old('length', $product->dimensions['length'] ?? '') }}">
                                            <label>الطول (سم)</label>
                                            @error('length')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('width') is-invalid @enderror" 
                                                   name="width" placeholder="العرض (سم)" 
                                                   value="{{ old('width', $product->dimensions['width'] ?? '') }}">
                                            <label>العرض (سم)</label>
                                            @error('width')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-floating">
                                            <input type="number" step="0.01" min="0" 
                                                   class="form-control @error('height') is-invalid @enderror" 
                                                   name="height" placeholder="الارتفاع (سم)" 
                                                   value="{{ old('height', $product->dimensions['height'] ?? '') }}">
                                            <label>الارتفاع (سم)</label>
                                            @error('height')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- خيارات إضافية -->
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3 mt-4">خيارات إضافية</h6>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="featured" 
                                                   id="featured" value="1" @checked(old('featured', $product->featured))>
                                            <label class="form-check-label" for="featured">
                                                منتج مميز
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="virtual" 
                                                   id="virtual" value="1" @checked(old('virtual', $product->virtual))>
                                            <label class="form-check-label" for="virtual">
                                                منتج افتراضي (لا يحتاج شحن)
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="downloadable" 
                                                   id="downloadable" value="1" @checked(old('downloadable', $product->downloadable))>
                                            <label class="form-check-label" for="downloadable">
                                                منتج قابل للتحميل
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('catalog_visibility') is-invalid @enderror" 
                                                    name="catalog_visibility" required>
                                                <option value="visible" @selected(old('catalog_visibility', $product->catalog_visibility) === 'visible')>ظاهر</option>
                                                <option value="catalog" @selected(old('catalog_visibility', $product->catalog_visibility) === 'catalog')>في الكتالوج فقط</option>
                                                <option value="search" @selected(old('catalog_visibility', $product->catalog_visibility) === 'search')>في البحث فقط</option>
                                                <option value="hidden" @selected(old('catalog_visibility', $product->catalog_visibility) === 'hidden')>مخفي</option>
                                            </select>
                                            <label>رؤية الكتالوج <span class="text-danger">*</span></label>
                                            @error('catalog_visibility')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- أزرار الحفظ -->
                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('woocommerce.products.show', $product->id) }}" class="btn btn-secondary">
                                                إلغاء
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fe fe-save"></i> تحديث المنتج
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
<script>
    function toggleStockQuantity() {
        const manageStock = document.getElementById('manage_stock');
        const stockQuantityField = document.getElementById('stock_quantity_field');
        
        if (manageStock.checked) {
            stockQuantityField.style.display = 'block';
        } else {
            stockQuantityField.style.display = 'none';
        }
    }

    // تشغيل عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        toggleStockQuantity();
    });
</script>
@stop

