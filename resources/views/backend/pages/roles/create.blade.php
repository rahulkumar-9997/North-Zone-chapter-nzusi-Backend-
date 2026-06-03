@extends('backend.layouts.master')
@section('title','Create Role')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Create Role</h4>
            <a href="{{ route('roles.index') }}"
                data-title="Create Role"
                class="btn btn-primary">
                <i data-feather="arrow-left" class="me-2"></i> Back to Role List
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf                
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="e.g., Admin, Editor, Viewer" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Slug <small class="text-muted">(Leave empty to auto-generate)</small></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug') }}" placeholder="e.g., admin, editor, viewer">
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>                    
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" checked>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="roleSubmitBtn">
                        {{ isset($role) ? 'Update' : 'Submit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<!-- <script src="{{ asset('backend/assets/js/pages/users.js') }}?v={{ env('APP_VERSION') }}"></script> -->
@endpush