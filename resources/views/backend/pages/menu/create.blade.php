@extends('backend.layouts.master')
@section('title','Create Menu')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Create Menu</h4>
            <a href="{{ route('menus.index') }}"
                data-title="Create Menu"
                class="btn btn-primary">
                <i data-feather="arrow-left" class="me-2"></i> Back to Menu List
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Menu Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g., Dashboard, Users">
                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="e.g., dashboard, users" >
                            @error('slug')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Icon Class</label>
                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="e.g., ti ti-home, fas fa-user">
                            <small class="text-muted">Use Font Awesome or Tabler Icons classes</small>
                            @error('icon')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Parent Menu</label>
                            <select name="parent_id" class="form-control select @error('parent_id') is-invalid @enderror">
                                <option value="">Main Menu (No Parent)</option>
                                @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Route Name</label>
                            <input type="text" name="route" class="form-control @error('route') is-invalid @enderror" value="{{ old('route') }}" placeholder="e.g., dashboard, users.index">
                            <small class="text-muted">Route name for Laravel named routes</small>
                            @error('route')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">URL (External Link)</label>
                            <input type="text" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url') }}" placeholder="https://example.com">
                            <small class="text-muted">Use for external links</small>
                            @error('url')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', 0) }}">
                            @error('order')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Target</label>
                            <select name="target" class="form-control @error('target') is-invalid @enderror">
                                <option value="_self" {{ old('target') == '_self' ? 'selected' : '' }}>Same Window (_self)</option>
                                <option value="_blank" {{ old('target') == '_blank' ? 'selected' : '' }}>New Window (_blank)</option>
                            </select>
                            @error('target')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox"
                                    name="status"
                                    value="1"
                                    class="custom-control-input"
                                    id="status"
                                    {{ old('status',1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveMenuBtn">
                        {{ isset($menu) ? 'Update' : 'Submit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    document.querySelector('input[name="name"]').addEventListener('keyup', function() {
        let slugInput = document.querySelector('input[name="slug"]');
        let slug = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    });
</script>
@endpush