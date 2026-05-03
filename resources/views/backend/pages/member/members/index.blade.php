@extends('backend.layouts.master')
@section('title','Member Lists')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Member Lists</h4>
            <div class="link-btn">
                <a href="{{ route('manage-member.import') }}"
                    class="btn btn-orange">
                    <i class="fa fa-file-alt me-2"></i> Import Member
                </a>
                <a href="{{ route('manage-member.create') }}"
                    class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i> Add New Member
                </a>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <div class="member-lists-table-render">
                    @include('backend.pages.member.members.partials.members-list', ['member_lists' => $member_lists ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<!-- <script src="{{ asset('backend/assets/js/pages/member-type.js') }}"></script> -->
@endpush