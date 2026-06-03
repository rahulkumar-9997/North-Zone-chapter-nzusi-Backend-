@extends('backend.layouts.master')
@section('title','User List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">User List</h4>
            <a href="{{ route('users.create') }}"
            data-title="Create User"
            class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New User
            </a>           
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">                
                <div class="user-list-table-render">
                    @include('backend.pages.users.partials.user-list', ['users' => $users ??[]])
                </div>                
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<!-- <script src="{{ asset('backend/assets/js/pages/users.js') }}?v={{ env('APP_VERSION') }}"></script> -->
@endpush