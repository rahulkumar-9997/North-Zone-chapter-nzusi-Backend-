@extends('backend.layouts.master')
@section('title','Member Type List')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Member Type List</h4>
            <a href="javascript:void(0);"
                data-route="{{ route('member-type.create') }}"
                data-size="lg"
                data-title="Create Member Type"
                data-member-type-add="true"
                data-format="simple"
                class="btn btn-primary">
                <i data-feather="plus" class="me-2"></i> Add New Member Type
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="member-type-list-table-render">
                    @include('backend.pages.member.member-type.partials.member-type-list', ['memberTypes' => $memberTypes ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/member-type.js') }}"></script>
@endpush