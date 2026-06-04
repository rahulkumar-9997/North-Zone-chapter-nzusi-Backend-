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
<script>    
    $(document).ready(function() {
        $('.show_confirm_users').click(function(event) {
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
