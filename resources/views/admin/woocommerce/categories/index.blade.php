@extends('admin.layouts.master')

@section('page-title')
    قائمة الفئات
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
                    <h5 class="page-title fs-21 mb-1">كافة الفئات</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('woocommerce.categories.create') }}" class="btn btn-success">
                        <i class="fe fe-plus"></i> إنشاء فئة جديدة
                    </a>
                    <form action="{{ route('woocommerce.categories.sync') }}" method="POST" class="d-inline">
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
                                <form action="{{ route('woocommerce.categories.index') }}" method="GET"
                                      class="d-flex align-items-center gap-2">
                                    <input style="width: 260px" type="text" name="search" class="form-control"
                                           placeholder="بحث بالاسم" value="{{ request('search') }}">

                                    <select name="parent_id" class="form-select">
                                        <option value="">كل الفئات</option>
                                        <option value="null" @selected(request('parent_id') === 'null')>الفئات الرئيسية فقط</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" @selected(request('parent_id') == $parent->id)>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                                    <a href="{{ route('woocommerce.categories.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الفئة</th>
                                        <th>الفئة الأب</th>
                                        <th>عدد المنتجات</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.categories.show', $category->id) }}" 
                                                   class="text-primary text-decoration-none">
                                                    <strong>{{ $category->name }}</strong>
                                                </a>
                                                @if($category->slug)
                                                    <br><small class="text-muted">{{ $category->slug }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($category->parent)
                                                    <a href="{{ route('woocommerce.categories.show', $category->parent->id) }}">
                                                        {{ $category->parent->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $category->count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('woocommerce.categories.show', $category->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('woocommerce.categories.edit', $category->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('woocommerce.categories.destroy', $category->id) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fe fe-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
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
                                {{ $categories->withQueryString()->links() }}
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

