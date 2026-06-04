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
<script>    
    $(document).ready(function() {
        $('.show_confirm_role').click(function(event) {
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