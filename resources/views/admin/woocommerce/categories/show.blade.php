@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الفئة: {{ $category->name }}
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

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الفئة: {{ $category->name }}</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.categories.index') }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة للقائمة
                    </a>
                    <a href="{{ route('woocommerce.categories.edit', $category->id) }}" class="btn btn-warning">
                        <i class="fe fe-edit"></i> تعديل
                    </a>
                    @if($category->woo_id)
                        <form action="{{ route('woocommerce.categories.destroy', $category->id) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fe fe-trash"></i> حذف
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الفئة</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="200">الاسم:</th>
                                    <td><strong>{{ $category->name }}</strong></td>
                                </tr>
                                @if($category->slug)
                                    <tr>
                                        <th>Slug:</th>
                                        <td>{{ $category->slug }}</td>
                                    </tr>
                                @endif
                                @if($category->parent)
                                    <tr>
                                        <th>الفئة الأب:</th>
                                        <td>
                                            <a href="{{ route('woocommerce.categories.show', $category->parent->id) }}">
                                                {{ $category->parent->name }}
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                                @if($category->description)
                                    <tr>
                                        <th>الوصف:</th>
                                        <td>{{ $category->description }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>عدد المنتجات:</th>
                                    <td><span class="badge bg-primary">{{ $category->count ?? 0 }}</span></td>
                                </tr>
                                @if($category->display)
                                    <tr>
                                        <th>طريقة العرض:</th>
                                        <td>{{ $category->display }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($category->children->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">الفئات الفرعية</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('woocommerce.categories.show', $child->id) }}" 
                                           class="list-group-item list-group-item-action">
                                            {{ $child->name }}
                                            <span class="badge bg-primary float-end">{{ $child->count ?? 0 }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- المنتجات المرتبطة بهذه الفئة -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">المنتجات المرتبطة بهذه الفئة</h5>
                            <span class="badge bg-primary">{{ $products->count() }} منتج</span>
                        </div>
                        <div class="card-body">
                            @if($products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>المنتج</th>
                                            <th>SKU</th>
                                            <th>السعر</th>
                                            <th>المخزون</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($products as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('woocommerce.products.show', $product->id) }}" 
                                                       class="text-primary text-decoration-none">
                                                        <strong>{{ \Illuminate\Support\Str::limit($product->name, 40) }}</strong>
                                                    </a>
                                                </td>
                                                <td>{{ $product->sku ?: '-' }}</td>
                                                <td>{{ number_format($product->price ?? $product->regular_price ?? 0, 2) }} ر.س</td>
                                                <td>
                                                    @if($product->stock_status === 'instock')
                                                        <span class="badge bg-success">متوفر</span>
                                                        @if($product->stock_quantity)
                                                            ({{ $product->stock_quantity }})
                                                        @endif
                                                    @elseif($product->stock_status === 'outofstock')
                                                        <span class="badge bg-danger">نفد</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $product->stock_status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $product->status === 'publish' ? 'success' : 'warning' }}">
                                                        {{ $product->status === 'publish' ? 'منشور' : 'مسودة' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('woocommerce.products.show', $product->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fe fe-eye"></i> عرض
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fe fe-package" style="font-size: 48px; opacity: 0.3;"></i>
                                    <p class="mt-3">لا توجد منتجات مرتبطة بهذه الفئة</p>
                                    <a href="{{ route('woocommerce.products.index') }}" class="btn btn-primary btn-sm">
                                        <i class="fe fe-plus"></i> إضافة منتج جديد
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    @if($category->image && is_array($category->image) && isset($category->image['src']))
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">صورة الفئة</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ $category->image['src'] }}" alt="{{ $category->name }}" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@stop

@section('js')
@stop

