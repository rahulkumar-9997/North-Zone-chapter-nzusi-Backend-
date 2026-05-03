@extends('backend.layouts.master')
@section('title','Blog Category List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Blog Category List</h4>
            <a href="javascript:void(0);" 
            data-route="{{ route('blog-category.create') }}"
            data-size="lg"
            data-title="Create Blog Category"
            data-blog-category-add="true"
            data-type="simple"
            class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New
            </a>           
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">                
                <div class="blog-category-list-table-render">
                    @include('backend.pages.blog.category.partials.category-list', ['blogCategories' => $blogCategories ??[]])
                </div>                
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/blog-category.js ') }}"></script>
@endpush