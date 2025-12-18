@extends('admin.layouts.master')

@section('page-title')
    تعديل العلامة: {{ $tag->name }}
@stop

@section('css')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل العلامة: {{ $tag->name }}</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.tags.show', $tag->id) }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة
                    </a>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات العلامة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('woocommerce.tags.update', $tag->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">اسم العلامة <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $tag->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Slug (اختياري)</label>
                                    <input type="text" name="slug" class="form-control" 
                                           value="{{ old('slug', $tag->slug) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">الوصف</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $tag->description) }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-save"></i> حفظ التغييرات
                                    </button>
                                    <a href="{{ route('woocommerce.tags.show', $tag->id) }}" class="btn btn-secondary">
                                        إلغاء
                                    </a>
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

