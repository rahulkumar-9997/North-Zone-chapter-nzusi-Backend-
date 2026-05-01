@extends('backend.layouts.master')
@section('title','Blog List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Blog List</h4>
            <a href="{{ route('blog-post.create') }}"
                class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Blog
            </a>
        </div>
        <div class="card-body p-1">
            <div class="table-responsive">
                @if($blogs->count() > 0)
                <div class="blog-category-list-table-render">
                    @include('backend.pages.blog.partials.blog-list', [
                    'blogCategories' => $blogCategories,
                    'blogs' =>$blogs
                    ])
                </div>
                @else
                <div class="text-center p-4">
                    <h4 class="mb-2">No Blog Found</h4>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/blog-category.js ') }}"></script>
<script>
    $(document).ready(function() {
      $('.show_confirm_blog').click(function(event) {
         var form = $(this).closest("form");
         var name = $(this).data("name");
         event.preventDefault(); 
         Swal.fire({
               title: `Are you sure you want to delete this ${name}?`,
               text: "If you delete this, it will be gone forever.",
               icon: "warning",
               showCancelButton: true,
               confirmButtonText: "Yes, delete it!",
               cancelButtonText: "Cancel",
               dangerMode: true,
         }).then((result) => {
               if (result.isConfirmed) {
                  form.submit();
               }
         });
      });
           
   });
</script>
@endpush