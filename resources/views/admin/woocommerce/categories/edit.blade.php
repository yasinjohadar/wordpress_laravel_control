@extends('admin.layouts.master')

@section('page-title')
    تعديل الفئة: {{ $category->name }}
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
                    <h5 class="page-title fs-21 mb-1">تعديل الفئة: {{ $category->name }}</h5>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('woocommerce.categories.show', $category->id) }}" class="btn btn-secondary">
                        <i class="fe fe-arrow-right"></i> العودة
                    </a>
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
                            <form action="{{ route('woocommerce.categories.update', $category->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $category->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Slug (اختياري)</label>
                                    <input type="text" name="slug" class="form-control" 
                                           value="{{ old('slug', $category->slug) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">الفئة الأب</label>
                                    <select name="parent_id" class="form-select">
                                        <option value="">لا يوجد (فئة رئيسية)</option>
                                        @foreach($parentCategories as $parent)
                                            <option value="{{ $parent->id }}" 
                                                    {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">الوصف</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">رابط الصورة</label>
                                    <input type="url" name="image" class="form-control" 
                                           value="{{ old('image', is_array($category->image) ? ($category->image['src'] ?? '') : '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">طريقة العرض</label>
                                    <select name="display" class="form-select">
                                        <option value="default" {{ old('display', $category->display ?? 'default') === 'default' ? 'selected' : '' }}>افتراضي</option>
                                        <option value="products" {{ old('display', $category->display ?? '') === 'products' ? 'selected' : '' }}>المنتجات فقط</option>
                                        <option value="subcategories" {{ old('display', $category->display ?? '') === 'subcategories' ? 'selected' : '' }}>الفئات الفرعية فقط</option>
                                        <option value="both" {{ old('display', $category->display ?? '') === 'both' ? 'selected' : '' }}>المنتجات والفئات الفرعية</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fe fe-save"></i> حفظ التغييرات
                                    </button>
                                    <a href="{{ route('woocommerce.categories.show', $category->id) }}" class="btn btn-secondary">
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

