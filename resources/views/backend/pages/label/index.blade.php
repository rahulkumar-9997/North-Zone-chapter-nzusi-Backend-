@extends('backend.layouts.master')
@section('title','Manage Label')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Label List</h4>
            <a href="javascript:void(0);"
                data-route="{{ route('label.create') }}"
                data-size="lg"
                data-title="Create Label"
                data-label-add="true"
                class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Label
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="label-list-table-render">
                    @include('backend.pages.label.partials.label-list', ['labels' => $labels ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/label.js') }}"></script>
@endpush