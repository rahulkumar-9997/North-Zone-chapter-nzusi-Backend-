@if(($mode ?? 'item') === 'list')
<div class="modal-body">
    <div class="mb-3">
        <h4 class="mb-1">
            Applicant Name
        </h4>
        <div class="fw-semibold">
            {{ \App\Helpers\MaskHelper::mask($submission->first_name, 1) }}
            {{ \App\Helpers\MaskHelper::mask($submission->last_name, 1) }}
        </div>
    </div>
    <div class="mb-3">
        <h4 class="mb-1">
            Abstract Title
        </h4>
        <div class="fw-semibold">
            {{ $submission->abstract_title }}
        </div>
    </div>
    <hr>
    <h6 class="fw-bold mb-3">
        <i class="fa-solid fa-users me-1"></i>
        Reviewer Details & Review History
    </h6>
    <div style="max-height:500px; overflow-y:auto;">
        @forelse($submission->reviews as $review)
        @include('backend.pages.abstract-reviewer.partials.review-item', ['mode' => 'item', 'review' => $review])
        @empty
        <div class="alert alert-danger mb-0">
            <i class="fa-solid fa-info-circle me-1"></i>
            No reviewer has submitted a review for this abstract yet.
        </div>
        @endforelse
    </div>
</div>
@else
@php
$badge = match ($review->status) {
'approved' => 'success',
'rejected' => 'danger',
default => 'warning',
};
@endphp
<div class="border rounded p-3 mb-3 bg-light">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="badge bg-{{ $badge }}">
                {{ ucfirst($review->status) }}
            </span>
            <span class="ms-2 fw-semibold">
                <i class="fa-solid fa-user me-1"></i>
                <strong>{{ optional($review->reviewer)->name ?? 'Unknown Reviewer' }}</strong>
            </span>
        </div>
        <small class="text-muted">
            {{ optional($review->created_at)->format('d M Y h:i A') }}
        </small>
    </div>

    @if($review->conflict_of_interest)
    <div class="mt-2">
        <span class="badge bg-danger">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Conflict of Interest
        </span>
    </div>
    @else
    <div class="mt-2">
        <span class="badge bg-success">
            <i class="fa-solid fa-circle-check me-1"></i>
            No Conflict of Interest
        </span>
    </div>
    @endif

    @if($review->presentationType)
    <div class="mt-2">
        <strong>Presentation Type:</strong>
        {{ $review->presentationType->name }}
    </div>
    @endif

    @if($review->presentationCategory)
    <div class="mt-2">
        <strong>Presentation Category:</strong>
        {{ $review->presentationCategory->name }}
    </div>
    @endif

    @if(!empty($review->other_category_text))
    <div class="mt-2">
        <strong>Other Category:</strong>
        {{ $review->other_category_text }}
    </div>
    @endif

    @if($review->total_score !== null)
    <div class="mt-2">
        <span class="badge bg-primary fs-18">
            <i class="fa-solid fa-star me-1"></i>
            Total Score: {{ $review->total_score }}
        </span>
    </div>
    @endif

    @if($review->scores->count())
    <div class="mt-3">
        <strong>Score Details:</strong>
        <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Criterion</th>
                        <th width="100" class="text-center">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($review->scores as $score)
                    <tr>
                        <td>{{ optional($score->criterion)->criterion ?? 'Criterion #' . $score->scientific_score_id }}</td>
                        <td class="text-center fw-bold">
                            {{ $score->score }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endif