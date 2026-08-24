@extends('backend.layouts.master')
@section('title','Review Abstract')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush
@section('main-content')
<div class="content">
    <div class="abstract-review-page">
        <div class="rev-card">
            <div class="rev-card-header">
                <h4 class="rev-title">Abstract Review</h4>
                <a href="{{ route('abstract-submission.index') }}" class="btn-rev-outline text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <ul class="nav nav-tabs rev-tabs" id="reviewTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane"
                        type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">
                        <i class="fa-solid fa-file-lines"></i> Abstract Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review-tab-pane"
                        type="button" role="tab" aria-controls="review-tab-pane" aria-selected="false">
                        <i class="fa-solid fa-clipboard-check"></i> Review &amp; Score
                        @if($existingReview)<span class="badge-lock"><i class="fa-solid fa-lock"></i></span>@endif
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="reviewTabContent">
                <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                    @include('backend.pages.abstract-reviewer.partials.review-abstract-details', ['submission' => $submission])
                </div>
                <div class="tab-pane fade" id="review-tab-pane" role="tabpanel" aria-labelledby="review-tab" tabindex="0">
                    @if($existingReview)
                        <div class="alert alert-success m-4" role="alert">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fa-solid fa-circle-check fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-2">
                                        Review Already Submitted
                                    </h5>
                                    <p class="mb-2">
                                        You have already submitted your review for this abstract.
                                    </p>
                                    <p class="mb-0">
                                        <strong>
                                            Reviews cannot be edited after submission.
                                        </strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        @include('backend.pages.abstract-reviewer.partials.review-guidelines-and-form', compact('submission','criteria','presentationTypes','presentationCategories','existingReview'))
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/pages/abstract-review-score.js') }}"></script>
@endpush