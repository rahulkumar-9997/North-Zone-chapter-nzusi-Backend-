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
                @if($blogCategories->count() > 0)
                <div class="blog-category-list-table-render">
                    @include('backend.pages.blog.category.partials.category-list', ['blogCategories' => $blogCategories])
                </div>
                @else
                <div class="text-center p-4">
                    <h4 class="mb-2">No Blog Category Found</h4>
                    <p class="mb-0">Start creating your first blog category.</p>
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
      $('.show_confirm').click(function(event) {
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