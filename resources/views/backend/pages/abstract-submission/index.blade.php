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