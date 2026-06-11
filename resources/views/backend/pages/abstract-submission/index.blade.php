@extends('backend.layouts.master')
@section('title','Abstract Submission')
@push('styles')
<style>
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
        font-size: 14px;
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="filter-section mb-3">
        <div id="example-2_wrapper" class="filter-box">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-filter text-primary"></i>
                        <h5 class="mb-0 fw-semibold">
                            Filter Abstract Submissions
                        </h5>
                    </div>
                </div>
                <div class="card-body p-2">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4 col-md-6">
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
                        <div class="col-lg-4 col-md-6">
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
                        <div class="col-lg-2 col-md-4">
                            <button
                                id="reset-button"
                                class="btn btn-danger w-100"
                                style="display:none;">
                                <i class="fa fa-refresh me-1"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Abstract Submission List</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="abstract-submission-list-table-render" id="abstract-list" data-url="{{ route('abstract-submission.index') }}">
                    @include('backend.pages.abstract-submission.partials.abstract-submission-list', ['abstractSubmissions' => $abstractSubmissions ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/abstract-review.js') }}"></script>
<script>
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

    $(document).ready(function() {
        function fetchAbstractSubmissions(page = 1) {
            let presentation_type = $('#member_type').val();
            let topic_category = $('#topic_category').val();
            let url = $('.abstract-submission-list-table-render').data('url');
            $("#loader").show();
            $.ajax({
                url: url,
                type: "GET",
                data: {
                    presentation_type: presentation_type,
                    topic_category: topic_category,
                    page: page
                },
                success: function(response) {
                    $('.abstract-submission-list-table-render').html(response);
                    $("#loader").hide();
                    toggleResetButton();
                },
                error: function() {
                    $("#loader").hide();
                    alert('Something went wrong.');
                }
            });
        }
        $('#member_type, #topic_category').on('change', function () {
            fetchAbstractSubmissions();
        });

        $(document).on(
            'click',
            '.pagination a',
            function(e) {
                e.preventDefault();
                let page = $(this)
                    .attr('href')
                    .split('page=')[1];
                fetchAbstractSubmissions(page);
            }
        );
        $('#reset-button').on('click', function() {
            $('#member_type').val('');
            $('#topic_category').val('');
            fetchAbstractSubmissions();
        });

         function toggleResetButton() {
            let presentation_type = $('#member_type').val();
            let topic_category = $('#topic_category').val();
            if (
                presentation_type !== '' ||
                topic_category !== ''
            ) {
                $("#reset-button").show();
            } else {
                $("#reset-button").hide();
            }
        }
        toggleResetButton();
    });
</script>

@endpush