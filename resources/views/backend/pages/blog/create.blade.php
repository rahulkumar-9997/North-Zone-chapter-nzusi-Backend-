@extends('backend.layouts.master')
@section('title','Create Blog')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Create Blog</h4>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <a href="{{ route('blog-post.index') }}" data-title="Go Back to Blog List Page" data-bs-toggle="tooltip" class="btn btn-sm btn-purple" data-bs-original-title="Go Back to Previous Page">
                << Go Back to Blog Page
            </a>
        </div>
        <div class="accordion-body border-top">
            <form action="{{ isset($blog) ? route('blog-post.update', $blog->id) : route('blog-post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($blog))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">
                                Select Category                                 
                                <span class="text-danger">*</span>
                                <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center btn-sm">
                                    Add New Category
                                </a>
                            </label>
                            <select class="select" name="blog_category">
                                <option value="">Select Category</option>
                                @foreach($blogCategories as $blogCategory)
                                    <option 
                                    value="{{ $blogCategory->id }}"
                                    {{ old('blog_category', $blog->category_id ?? '') == $blogCategory->id ? 'selected' : '' }}>{{ $blogCategory->title }}</option>
                                @endforeach
                            </select>
                            @error('blog_category')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Blog Title 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" class="form-control"  value="{{ old('title', $blog->title ?? '') }}">
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Reading Title</label>
                            <input type="text" name="reading_title" class="form-control" value="{{ old('reading_title', $blog->reading_title ?? '') }}">
                            @error('reading_title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="select" name="status">
                                <option value="1" {{ old('status', $blog->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $blog->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image_file" class="form-control">
                            @if(isset($blog) && $blog->image_file)
                                <img src="{{ asset($blog->image_file) }}" width="80" class="mt-2">
                            @endif
                            @error('image_file') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">PDF Title</label>
                            <input type="text" name="pdf_file_title" class="form-control" value="{{ old('pdf_file_title', $blog->pdf_file_title ?? '') }}">
                            @error('pdf_file_title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">PDF File</label>
                            <input type="file" name="pdf_file" class="form-control">
                            @if(isset($blog) && $blog->pdf_file)
                                <a href="{{ asset($blog->pdf_file) }}" target="_blank">View PDF</a>
                            @endif
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Meta Title 
                            </label>
                            <input type="text" name="meta_title" class="form-control"  value="{{ old('meta_title', $blog->meta_title ?? '') }}">
                            @error('meta_title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                            @error('meta_description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Short Content</label>
                            <textarea name="short_content" class="form-control" rows="3">{{ old('short_content', $blog->short_content ?? '') }}</textarea>
                            @error('short_content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Long Content</label>
                            <textarea name="long_content" class="form-control ckeditorUpdate4"> {{ old('long_content', $blog->long_content ?? '') }}</textarea>
                            @error('long_content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <input type="hidden" name="post_user" value="{{ auth()->id() }}">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('blog-post.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/ckeditor-4/ckeditor.js') }}?v={{ env('ASSET_VERSION', '1.0') }}"></script>
<script>
window.CKEDITOR_ROUTES = {
    upload: "{{ route('ckeditor.upload') }}",
    imagelist: "{{ route('ckeditor.images') }}",
    delete: "{{ route('ckeditor.delete') }}"
};
window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('backend/assets/ckeditor-4/ckeditor-r-create-config.js') }}?v={{ env('ASSET_VERSION', '1.0') }}">
</script>
<script>
    document.querySelectorAll('.ckeditorUpdate4').forEach(function(el) {
        CKEDITOR.replace(el, {
            removePlugins: 'exportpdf'
        });
    });
    $(document).ready(function() {
       $("form").on("submit", function (e) {
            let $form = $(this);
            let $btn = $form.find("button[type='submit']");
            if ($btn.length) {
                $btn.prop("disabled", true);
                let $spinner = $btn.find(".spinner-border");
                let $text = $btn.find(".btn-text");
                if ($spinner.length) $spinner.removeClass("d-none");
                if ($text.length) $text.text("Please wait...");
            }
        });
    });
</script>
@endpush