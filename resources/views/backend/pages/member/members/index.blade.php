@extends('backend.layouts.master')
@section('title','Member Lists')
@push('styles')
<style>
    .sort-btn {
        color: #adb5bd;
        font-size: 12px;
        line-height: 1;
    }
    .sort-btn:hover {
        color: #0d6efd;
    }
    .sort-btn.text-primary {
        color: #0d6efd !important;
    }
    th {
        position: relative;
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="filter-section">
        <div id="example-2_wrapper" class="filter-box">
            <div class="d-flex flex-wrap align-items-center bg-white p-2 gap-3">
                <input type="hidden" id="current_sort_by" value="">
                <input type="hidden" id="current_sort_order" value="">                
                <div class="d-flex align-items-center border-end pe-1">
                    <p class="mb-0 me-2 text-dark-grey f-14">Member Type:</p>
                    <select id="member_type" class="form-select form-select-md">
                        <option value="">Select Member Type</option>
                        @foreach ($members_type as $member_type)
                            <option value="{{ $member_type->id }}">{{ $member_type->title }}</option>
                        @endforeach                        
                    </select>
                </div>
                <div class="d-flex align-items-center border-end pe-1">
                    <p class="mb-0 me-2 text-dark-grey f-14">Status:</p>
                    <select id="member_status" name="status" class="form-select form-select-md">
                        <option value="">Select Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="d-flex align-items-center">
                    <label class="mb-0 me-2 text-dark-grey f-14">Search:</label>
                    <input type="search" class="form-control form-control-md" id="member_key" placeholder="Search Membership No/Name/Email/Mobile">
                </div>
                <button id="reset-button" class="btn btn-danger" style="display:none;">
                    <i class="fa fa-refresh"></i>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>
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
<script src="{{ asset('backend/assets/js/pages/member-filter.js') }}?v={{ config('app.assets_version') }}"></script>
<script>
    window.routes = {
        memberIndex: "{{ route('manage-member.index') }}"
    };
</script>
<script>
    $(document).ready(function () {
        var headerHeight = $('.header').outerHeight();
        var footer = $('.filter-box');
        var card = $('.filter-section'); 
        if (footer.length) {
            var footerOffset = footer.offset().top;
            console.log(footerOffset);
        } else {
            console.log("Footer not found!");
        }
        function updateFooterWidth() {
            footer.css('width', card.outerWidth() + 'px');
        }
        $(window).on('scroll resize', function () {
            if ($(window).scrollTop() > footerOffset - headerHeight) {
                footer.addClass('client-list-filter').css('top', headerHeight + 'px');
                updateFooterWidth();
            } else {
                footer.removeClass('client-list-filter').css('width', '');
            }
        });
        $(window).resize(updateFooterWidth);
    });
 </script>
 
@endpush