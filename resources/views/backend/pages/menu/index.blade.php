@extends('backend.layouts.master')
@section('title','Menu List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Menu List</h4>
            <a href="{{ route('menus.create') }}"
                data-title="Create Menu"
                class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Menu
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="user-list-table-render"  data-order-url="{{ route('menus.update-order', ':id') }}" data-status-url="{{ route('menus.toggle-status', ':id') }}" data-sidebar-url="{{ route('menus.toggle-sidebar-status', ':id') }}">
                    @include('backend.pages.menu.partials.menu-list', ['menus' => $menus ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/menu.js') }}?v={{ env('APP_VERSION') }}"></script>
<script>    
    $(document).ready(function() {
        $('.show_confirm_menu').click(function(event) {
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