@extends('admin.layouts.master')

@section('page-title')
    قائمة المنتجات
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
                    <h5 class="page-title fs-21 mb-1">كافة المنتجات</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('woocommerce.products.create') }}" class="btn btn-success">
                        <i class="fe fe-plus"></i> إنشاء منتج جديد
                    </a>
                    <form action="{{ route('woocommerce.products.sync') }}" method="POST" class="d-inline">
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
                                <form action="{{ route('woocommerce.products.index') }}" method="GET"
                                      class="d-flex align-items-center gap-2">
                                    <input style="width: 260px" type="text" name="search" class="form-control"
                                           placeholder="بحث بالاسم أو SKU" value="{{ request('search') }}">

                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="publish" @selected(request('status') === 'publish')>منشور</option>
                                        <option value="draft" @selected(request('status') === 'draft')>مسودة</option>
                                    </select>

                                    <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                                    <a href="{{ route('woocommerce.products.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>المنتج</th>
                                        <th>SKU</th>
                                        <th>السعر</th>
                                        <th>المخزون</th>
                                        <th>الحالة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.products.show', $product->id) }}" 
                                                   class="text-primary text-decoration-none">
                                                    {{ \Illuminate\Support\Str::limit($product->name, 40) }}
                                                </a>
                                            </td>
                                            <td>{{ $product->sku ?: '-' }}</td>
                                            <td>{{ number_format($product->price ?? 0, 2) }} ر.س</td>
                                            <td>
                                                @if($product->stock_status === 'instock')
                                                    <span class="badge bg-success">متوفر</span>
                                                @elseif($product->stock_status === 'outofstock')
                                                    <span class="badge bg-danger">نفد</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $product->stock_status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->status === 'publish')
                                                    <span class="badge bg-success">منشور</span>
                                                @elseif($product->status === 'draft')
                                                    <span class="badge bg-warning text-dark">مسودة</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $product->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $products->withQueryString()->links() }}
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


