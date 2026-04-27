@extends('backend.layouts.master')
@section('title','Create Pages')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4 class="fw-bold">Edit Page</h4>
                <h6>Manage Pages</h6>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <a href="{{ url()->previous() }}" data-title="Go Back to Category" data-bs-toggle="tooltip" class="btn btn-sm btn-purple" data-bs-original-title="Go Back to Previous Page">
                &lt;&lt; Go Back to Previous Page
            </a>
        </div>
        <div class="accordion-body border-top">
            <form action="{{ isset($page) ? route('pages.update', $page) : route('pages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($page))
                @method('PUT')
                @endif
                <div class="row">
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Page Title<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="title" value="{{ old('title', $page->title ?? '') }}">
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Route Name</label>
                            <input type="text" class="form-control" name="route_name" value="{{ old('route_name', $page->route_name ?? '') }}">
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Main Image</label>
                            <input type="file" class="form-control" name="main_image">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="summer-description-box mb-3">
                            <label class="form-label">Page Content</label>
                            <textarea name="content" class="ckeditorUpdate4">{{ old('content', $page->content ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Parent Page</label>
                            <select class="select" name="parent_id">
                                <option value="">-- No Parent --</option>
                                @foreach($pages as $page_row)
                                <option value="{{ $page_row->id }}"
                                   {{ old('parent_id', $page->parent_id ?? '') == $page_row->id ? 'selected' : '' }}>
                                    {{ $page_row->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 col-12">
                        <div class="mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" name="order" value="{{ old('order', $page->order ?? 0) }}">
                        </div>
                    </div>
                    <div class="col-sm-2 col-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label><br>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="is_active"
                                    id="is_active"
                                    value="1"
                                    {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2 col-12">
                        <div class="mb-3">
                            <label class="form-label">Show in Sidebar</label><br>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="show_in_sidebar"
                                    id="show_in_sidebar"
                                    value="1"
                                    {{ old('show_in_sidebar', $page->show_in_sidebar ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Include in Sidebar</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title', $page->meta_title ?? '') }}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="form-control">{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center justify-content-end mb-4">
                            <a href="{{ route('pages.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">{{ isset($page) ? 'Update Page' : 'Add Page' }}</button>
                        </div>
                    </div>
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