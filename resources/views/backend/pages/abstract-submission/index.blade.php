@extends('backend.layouts.master')
@section('title','Abstract Submission')
@push('styles')
@endpush
@section('main-content')
<div class="content">
    <div class="filter-section">
        <div id="example-2_wrapper" class="filter-box">
            <div class="d-flex flex-wrap align-items-center bg-white p-2 gap-3">
                <div class="d-flex align-items-center border-end pe-1">
                    <p class="mb-0 me-2 text-dark-grey f-14">Presentation Type:</p>
                    <select id="member_type" class="form-select form-select-md">
                        <option value="">Select Presentation Type</option>
                        <option value="video">Video Presentation (BV)</option>
                        <option value="podium">Podium / Best Paper (BP)</option>
                        <option value="poster">Moderated Poster (BPos)</option>
                        <option value="eposter">Unmoderated e-Poster (UPos)</option>
                    </select>
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
            <h4 class="card-title">Abstract Submission List</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="abstract-submission-list-table-render" data-url="{{ route('abstract-submission.index') }}">
                    @include('backend.pages.abstract-submission.partials.abstract-submission-list', ['abstractSubmissions' => $abstractSubmissions ??[]])
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        function fetchAbstractSubmissions(page = 1) {
            let presentation_type = $('#member_type').val();
            let url = $('.abstract-submission-list-table-render').data('url');
            $("#loader").show();
            $.ajax({
                url: url,
                type: "GET",
                data: {
                    presentation_type: presentation_type,
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
        $('#member_type').on('change', function() {
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
            fetchAbstractSubmissions();
        });
        function toggleResetButton() {
            let presentation_type = $('#member_type').val();
            if (presentation_type !== '') {
                $("#reset-button").show();
            } else {
                $("#reset-button").hide();
            }
        }
        toggleResetButton();
    });
</script>

@endpush