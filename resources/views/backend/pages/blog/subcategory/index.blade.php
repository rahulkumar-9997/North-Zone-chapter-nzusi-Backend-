@extends('backend.layouts.master')
@section('title','Blog Subcategory List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Blog Subcategory List</h4>
            <a href="javascript:void(0);" 
            data-route="{{ route('blog-subcategory.create') }}"
            data-size="lg"
            data-title="Create Blog Subcategory"
            data-blog-subcategory-add="true"
            data-type="simple"
            class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Subcategory
            </a>           
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">                
                <div class="blog-subcategory-list-table-render">
                    @include('backend.pages.blog.subcategory.partials.subcategory-list', ['subcategories' => $subcategories ??[]])
                </div>                
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/blog-subcategory.js') }}?v={{ env('APP_VERSION') }}"></script>
@endpush