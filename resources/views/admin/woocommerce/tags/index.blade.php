@extends('admin.layouts.master')

@section('page-title')
    قائمة العلامات
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
                    <h5 class="page-title fs-21 mb-1">كافة العلامات</h5>
                </div>
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('woocommerce.tags.create') }}" class="btn btn-success">
                        <i class="fe fe-plus"></i> إنشاء علامة جديدة
                    </a>
                    <form action="{{ route('woocommerce.tags.sync') }}" method="POST" class="d-inline">
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
                                <form action="{{ route('woocommerce.tags.index') }}" method="GET"
                                      class="d-flex align-items-center gap-2">
                                    <input style="width: 260px" type="text" name="search" class="form-control"
                                           placeholder="بحث بالاسم" value="{{ request('search') }}">

                                    <button type="submit" class="btn btn-secondary btn-sm">بحث</button>
                                    <a href="{{ route('woocommerce.tags.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم العلامة</th>
                                        <th>عدد المنتجات</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($tags as $tag)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('woocommerce.tags.show', $tag->id) }}" 
                                                   class="text-primary text-decoration-none">
                                                    <strong>{{ $tag->name }}</strong>
                                                </a>
                                                @if($tag->slug)
                                                    <br><small class="text-muted">{{ $tag->slug }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $tag->count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('woocommerce.tags.show', $tag->id) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fe fe-eye"></i>
                                                    </a>
                                                    <a href="{{ route('woocommerce.tags.edit', $tag->id) }}" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fe fe-edit"></i>
                                                    </a>
                                                    <form action="{{ route('woocommerce.tags.destroy', $tag->id) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه العلامة؟');">
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
                                            <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $tags->withQueryString()->links() }}
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

