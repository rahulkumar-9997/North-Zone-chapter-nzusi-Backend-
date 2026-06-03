@extends('backend.layouts.master')
@section('title','Blog List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="filter-section">
        <div id="example-2_wrapper" class="filter-box" style="top: 65px; width: 1488px;">
            <div class="d-flex flex-wrap align-items-center bg-white p-2 gap-3">
                <div class="d-flex align-items-center border-end pe-1">
                    <p class="mb-0 me-2 text-dark-grey f-14">Category:</p>
                    <select id="blog_category" name="category" class="form-select form-select blog_category">
                        <option value="">Select Category</option>
                        @foreach($blogCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex align-items-center border-end pe-1">
                    <p class="mb-0 me-2 text-dark-grey f-14">Subcategory:</p>
                    <select id="blog_subcategory" name="subcategory" class="form-select form-select blog_subcategory">
                        <option value="">Select Subcategory</option>
                        @foreach($blogSubcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">{{ $subcategory->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button id="reset-button" class="btn btn-danger" style="display:none;">
                    <i class="fa fa-refresh"></i>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>
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

        $("#blog_category").on("change", function () {
            updateFilters();
        });

        $("#blog_subcategory").on("change", function () {
            updateFilters();
        });

        $("#reset-button").on("click", function() {
            $("#blog_category").val("");
            $("#blog_subcategory").val("");
            $(this).hide();
            fetchBlogs();
        });

        $(document).on("click", ".pagination a", function(e) {
            e.preventDefault();
            let page = $(this).attr("href").split("page=")[1];
            let categoryId = $("#blog_category").val();
            let blog_subcategory = $("#blog_subcategory").val();
            fetchBlogs(
                categoryId,
                blog_subcategory,
                page
            );
        });

        function fetchBlogs(
            categoryId = "",
            blog_subcategory = "",
            page = 1
        ) {
            $("#loader").show();
            $.ajax({
                url: "{{ route('blog-post.index') }}",
                type: "GET",
                data: {
                    category_id: categoryId,
                    blog_subcategory: blog_subcategory,
                    page: page
                },
                success: function(data) {
                    $(".blog-category-list-table-render").html(data);
                    $("#loader").hide();
                    if (
                        categoryId !== "" ||
                        blog_subcategory !== ""
                    ) {
                        $("#reset-button").show();
                    } else {
                        $("#reset-button").hide();
                    }
                },
                error: function() {
                    $("#loader").hide();
                    Toastify({
                        text: "Error loading blogs.",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        className: "bg-danger"
                    }).showToast();
                }
            });
        }

        function updateFilters()
        {
            let categoryId = $("#blog_category").val();
            let blog_subcategory = $("#blog_subcategory").val();
            fetchBlogs(
                categoryId,
                blog_subcategory
            );
        }
    });
</script>
@endpush