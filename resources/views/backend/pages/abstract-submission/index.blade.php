@extends('backend.layouts.master')
@section('title','Abstract Submission')
@push('styles')
<style>
    .select2-dropdown {
        width: auto !important;
        max-width: 280px;
        min-width: 220px;
    }
    .reviewer-select-wrapper {
        min-width: 200px;
        max-width: 260px;
    }
    .assign-reviewer-select + .select2-container {
    width: 100% !important;
    min-width: 180px;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        max-height: 100px;
        overflow-y: auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap;
        gap: 4px;
    }

    .abstract-title-column,
    td:has(.assign-reviewer-select) {
        min-width: 220px;
    }
    .abstract-title-column {
        white-space: normal !important;
        min-width: 250px;
    }
    .status-btn {
        cursor: pointer;
    }

    .status-badge {
        font-size: 11px;
        padding: 5px 5px;
        transition: all 0.2s ease-in-out;
    }

    /* Hover effect */
    .status-btn:hover .status-badge {
        transform: scale(1.05);
        /* box-shadow: 0 4px 12px rgba(0,0,0,0.15); */
    }

    /* subtle glow */
    .status-btn:hover {
        opacity: 0.9;
    }
    #abstract-list .table tbody tr td{
        font-size: 13px;
    }
</style>
<style>
    #abstract-list .abs-table-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #abstract-list .abs-table {
        width: 100%;
        min-width: 1050px;
        margin-bottom: 0;
    }

    #abstract-list .abs-table th {
        white-space: nowrap;
        font-size: 13px;
        vertical-align: middle;
    }

    #abstract-list .abs-table td {
        font-size: 13px;
        vertical-align: top;
    }

    /* ---------- Column widths ---------- */
    #abstract-list .col-sno {
        width: 45px;
    }

    #abstract-list .col-participant {
        width: 260px;
    }

    #abstract-list .col-status {
        width: 105px;
    }

    #abstract-list .col-reviewers {
        width: 200px;
    }

    #abstract-list .col-abstract {
        min-width: 240px;
        white-space: normal;
    }

    #abstract-list .col-actions {
        width: 230px;
        min-width: 230px;
    }

    /* ---------- Sticky Actions column ---------- */
    #abstract-list .abs-table th.col-actions,
    #abstract-list .abs-table td.col-actions {
        position: sticky;
        right: 0;
        z-index: 2;
        box-shadow: -4px 0 6px -4px rgba(0, 0, 0, .15);
    }

    #abstract-list .abs-table td.col-actions {
        background: #fff;
    }

    #abstract-list .abs-table th.col-actions {
        background: #212529;
    }

    #abstract-list .abs-table tbody tr:hover td.col-actions {
        background: #f5f5f5;
    }

    /* ---------- Participant ---------- */
    #abstract-list .contact-line {
        font-size: 12px;
        word-break: break-all;
        line-height: 1.4;
    }

    #abstract-list .abs-badges .badge {
        font-size: 10.5px;
        font-weight: 500;
    }

    /* ---------- Status ---------- */
    #abstract-list .status-btn {
        cursor: pointer;
    }

    #abstract-list .status-badge {
        font-size: 11px;
        padding: 5px 7px;
        transition: transform .2s ease-in-out;
    }

    #abstract-list .status-btn:hover .status-badge {
        transform: scale(1.05);
    }

    #abstract-list .review-progress {
        font-size: 11px;
    }

    /* ---------- Reviewers ---------- */
    #abstract-list .reviewer-group-title {
        display: block;
        font-weight: 600;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 3px;
    }

    #abstract-list .reviewer-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }

    #abstract-list .reviewer-name {
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 500;
    }

    #abstract-list .reviewer-item .badge {
        font-size: 10.5px;
        min-width: 28px;
    }

    #abstract-list .reviewer-date {
        font-size: 10.5px;
        color: #6c757d;
        margin-bottom: 4px;
        padding-bottom: 3px;
        border-bottom: 1px dashed #e9ecef;
    }

    #abstract-list .reviewer-group .reviewer-date:last-child {
        border-bottom: 0;
        margin-bottom: 0;
    }

    /* ---------- Abstract ---------- */
    #abstract-list .abstract-title {
        font-weight: 600;
        color: #212529;
        line-height: 1.35;
    }

    #abstract-list .abstract-meta {
        font-size: 12px;
        color: #6c757d;
    }

    /* ---------- Actions ---------- */
    #abstract-list .col-actions .select2-container {
        width: 100% !important;
    }

    #abstract-list .col-actions .select2-selection--multiple {
        min-height: 34px;
        max-height: 80px;
        overflow-y: auto;
    }

    #abstract-list .action-btns .btn {
        font-size: 12px;
        padding: 3px 8px;
        white-space: nowrap;
    }

    .select2-results__option[aria-disabled="true"] {
        color: #198754 !important;
        font-style: italic;
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="filter-section mb-3">
        <div id="example-2_wrapper" class="filter-box">
            <div class="card border-0 shadow-sm">                
                <div class="card-body p-2">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                Presentation Type
                            </label>
                            <select id="member_type" class="form-select form-select-md">
                                <option value="">
                                    All Presentation Types
                                </option>
                                <option value="video">
                                    Video Presentation (BV)
                                </option>
                                <option value="podium">
                                    Podium / Best Paper (BP)
                                </option>
                                <option value="poster">
                                    Moderated Poster (BPos)
                                </option>
                                <option value="eposter">
                                    Unmoderated e-Poster (UPos)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">
                                Topic / Category
                            </label>
                            <select id="topic_category" class="form-select form-select-md">
                                <option value="">
                                    All Topic / Category 
                                </option>
                                <option value="Endourology & Stone Disease">
                                    Endourology & Stone Disease
                                </option>
                                <option value="Uro-oncology">
                                    Uro-oncology
                                </option>
                                <option value="Reconstructive Urology">
                                    Reconstructive Urology
                                </option>
                                <option value="Female Urology & Incontinence">
                                    Female Urology & Incontinence
                                </option>
                                <option value="Andrology & Sexual Medicine">
                                    Andrology & Sexual Medicine
                                </option>
                                <option value="Paediatric Urology">
                                    Paediatric Urology
                                </option>
                                <option value="Renal Transplantation">
                                    Renal Transplantation
                                </option>
                                <option value="Laparoscopy & Robotics">
                                    Laparoscopy & Robotics
                                </option>
                                <option value="Trauma & Emergency Urology">
                                    Trauma & Emergency Urology
                                </option>
                                <option value="Infections & Inflammation">
                                    Infections & Inflammation
                                </option>
                                <option value="Other">
                                    Other
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" id="filter_name" class="form-control" placeholder="Search by name">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="text" name="date_from" id="date_from" class="form-control datepicker">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="text" name="date_to" id="date_to" class="form-control datepicker">
                        </div>
                        <div class="col-md-2">
                            <button
                                id="reset-button"
                                class="btn btn-danger w-100"
                                style="display:none;">
                                <i class="fa fa-refresh me-1"></i>
                                Reset
                            </button>
                        </div>
                        <div class="col-md-2">
                            @if(auth()->user()->is_admin == 1 || auth()->user()->hasAnyRole(['webadmin', 'admin']))
                               <a href="{{ route('abstract-submission.export') }}"
                                    id="export-excel-btn"
                                    class="btn btn-success">
                                    <i class="fa-solid fa-file-excel me-1"></i>
                                    <span class="btn-text">Export to Excel</span>
                                </a>
                            @endif
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Abstract Submission List</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('abstract-review.guidelines') }}" target="_blank" class="btn btn-danger">
                    <i class="fa-solid fa-book me-1"></i> Review Guidelines
                </a>               
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive1">
                <div class="abstract-submission-list-table-render" id="abstract-list" data-url="{{ route('abstract-submission.index') }}">
                    @include('backend.pages.abstract-submission.partials.abstract-submission-list', ['abstractSubmissions' => $abstractSubmissions ?? [], 'isAdmin' => $isAdmin ?? false, 'maskVisible' => $maskVisible ?? 0])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/abstract-review.js') }}?v={{ config('app.assets_version') }}"></script>
<script>
$(document).ready(function(){
    let fromPicker = $('#date_from').flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        maxDate: "today", 
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            toPicker.set('minDate', dateStr);
        }
    });

    let toPicker = $('#date_to').flatpickr({
        enableTime: false,
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d M Y",
        maxDate: "today",
        disableMobile: true,
        onChange: function(selectedDates, dateStr) {
            fromPicker.set('maxDate', dateStr || "today");
        }
    });
});
</script>
<script>
    /*Excel Export abstarct list */
    $('#export-excel-btn').on('click', function(e) {
        e.preventDefault();
        let $btn = $(this);
        if ($btn.hasClass('disabled')) {
            return;
        }
        let params = $.param({
            presentation_type: $('#member_type').val(),
            topic_category: $('#topic_category').val(),
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
        });
        let exportUrl = $btn.attr('href') + '?' + params;
        $btn.addClass('disabled').attr('aria-disabled', 'true');
        $btn.find('.btn-text').text('Exporting...');
        window.location.href = exportUrl;
        setTimeout(function() {
            $btn.removeClass('disabled').removeAttr('aria-disabled');
            $btn.find('.btn-text').text('Export to Excel');
        }, 3000);
    });
    /*Excel Export abstarct list */
    $(document).ready(function() {
        $('.delete_abstract').click(function(event) {
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