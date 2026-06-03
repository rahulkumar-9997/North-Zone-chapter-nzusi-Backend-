@extends('backend.layouts.master')
@section('title','Role List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Role List</h4>
            <a href="{{ route('roles.create') }}"
            data-title="Create Role"
            class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Role
            </a>           
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">                
                <div class="user-list-table-render">
                    @include('backend.pages.roles.partials.role-list', ['roles' => $roles ??[]])
                </div>                
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<!-- <script src="{{ asset('backend/assets/js/pages/users.js') }}?v={{ env('APP_VERSION') }}"></script> -->
@endpush