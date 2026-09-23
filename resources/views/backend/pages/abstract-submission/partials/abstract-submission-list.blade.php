@php
$authUser = auth()->user();
$isAdmin = $isAdmin ?? ($authUser->is_admin == 1 || $authUser->hasAnyRole(['webadmin', 'admin']));

$presentationLabels = [
'video' => 'Video Presentation (BV)',
'podium' => 'Podium / Best Paper (BP)',
'poster' => 'Moderated Poster (BPos)',
'eposter' => 'Unmoderated e-Poster (UPos)',
];
@endphp
<div class="abs-table-wrap">
    <table class="table table-hover table-bordered abs-table">
        <thead class="table-dark">
            <tr>
                <th class="col-sno text-center">#</th>
                <th class="col-participant">Participant Details</th>
                <th class="col-status text-center">Status</th>
                <th class="col-reviewers">{{ $isAdmin ? 'Reviewers' : 'Your Review' }}</th>
                <th class="col-abstract">Abstract</th>
                <th class="col-actions text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($abstractSubmissions as $submission)
            @php
            $completedReviews = $submission->reviews;
            $pendingAssignments = $submission->assignments;

            $reviewedIds = $completedReviews->pluck('reviewed_by')->map(fn ($id) => (int) $id)->all();
            $assignedIds = $pendingAssignments->pluck('assigned_to')->map(fn ($id) => (int) $id)->all();

            $myReview = $completedReviews->firstWhere('reviewed_by', $authUser->id);
            @endphp
            <tr>
                {{-- # --}}
                <td class="col-sno fw-bold text-center">
                    {{ $loop->iteration + ($abstractSubmissions->currentPage() - 1) * $abstractSubmissions->perPage() }}
                </td>

                {{-- Participant + Contact --}}
                <td class="col-participant">
                    <div class="fw-semibold text-dark mb-1">
                        {{ \App\Helpers\MaskHelper::mask($submission->first_name, 0) }}
                        {{ \App\Helpers\MaskHelper::mask($submission->last_name, 0) }}
                    </div>

                    <div class="abs-badges d-flex flex-wrap gap-1 mb-1">
                        @if($submission->abstract_id)
                        <span class="badge bg-dark" data-bs-toggle="tooltip" title="Unique Abstract Submission ID">
                            <i class="fa-solid fa-id-badge me-1"></i>
                            {{ \App\Helpers\MaskHelper::maskLimit($submission->abstract_id, 20, 0) }}
                        </span>
                        @endif
                        @if($submission->nzusi_membership_no)
                        <span class="badge bg-primary" data-bs-toggle="tooltip" title="NZUSI Membership Number">
                            NZUSI: {{ \App\Helpers\MaskHelper::maskLimit($submission->nzusi_membership_no, 20, 0) }}
                        </span>
                        @endif
                        @if($submission->usi_membership_no)
                        <span class="badge bg-secondary" data-bs-toggle="tooltip" title="USI Membership Number">
                            USI: {{ \App\Helpers\MaskHelper::maskLimit($submission->usi_membership_no, 20, 0) }}
                        </span>
                        @endif
                    </div>

                    @if($submission->phone)
                    <div class="contact-line">
                        <i class="fa-solid fa-phone text-muted me-1"></i>
                        <a href="tel:{{ \App\Helpers\MaskHelper::mask($submission->phone, 0) }}" class="text-decoration-none">
                            {{ \App\Helpers\MaskHelper::mask($submission->phone, 0) }}
                        </a>
                    </div>
                    @endif
                    @if($submission->email)
                    <div class="contact-line">
                        <i class="fa-solid fa-envelope text-muted me-1"></i>
                        <a href="mailto:{{ \App\Helpers\MaskHelper::mask($submission->email, 0) }}" class="text-decoration-none">
                            {{ \App\Helpers\MaskHelper::mask($submission->email, 0) }}
                        </a>
                    </div>
                    @endif
                    <div class="contact-line text-muted">
                        <i class="fa-regular fa-clock me-1"></i>
                        {{ optional($submission->created_at)->format('d M Y h:i A') }}
                    </div>

                    @if($submission->supporting_file)
                    <a href="{{ asset('storage/images/abstract-submission/' . $submission->supporting_file) }}"
                        target="_blank" class="btn btn-sm btn-outline-primary mt-1 py-0 px-2">
                        <i class="fa-solid fa-file-pdf me-1"></i> View File
                    </a>
                    @endif
                </td>

                {{-- Status --}}
                <td class="col-status text-center">
                    <button class="btn btn-sm border-0 p-0 open-review-modal status-btn"
                        data-status="{{ $submission->status }}"
                        data-title="{{ $submission->first_name }}"
                        data-size="xl"
                        data-route="{{ route('abstract-review.show', $submission->id) }}"
                        data-abstract="true"
                        title="Click to view reviews">
                        @if($submission->status == 'pending')
                        <span class="badge bg-warning status-badge"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                        @elseif($submission->status == 'approved')
                        <span class="badge bg-success status-badge"><i class="fa-solid fa-check me-1"></i>Reviewed</span>
                        @else
                        <span class="badge bg-danger status-badge"><i class="fa-solid fa-xmark me-1"></i>Rejected</span>
                        @endif
                    </button>

                    @if($isAdmin)
                    <div class="review-progress text-muted mt-1">
                        {{ $completedReviews->count() }} / {{ $completedReviews->count() + $pendingAssignments->count() }} done
                    </div>
                    @endif
                </td>

                {{-- Reviewers --}}
                <td class="col-reviewers">
                    @if($completedReviews->isNotEmpty())
                    <div class="reviewer-group">
                        <span class="reviewer-group-title text-success">
                            <i class="fa-solid fa-circle-check me-1"></i>Reviewed by
                        </span>
                        @foreach($completedReviews as $review)
                        <div class="reviewer-item">
                            <span class="reviewer-name" title="{{ $review->reviewer->name ?? 'Deleted User' }}">
                                {{ $review->reviewer->name ?? 'Deleted User' }}
                            </span>
                            @if($review->conflict_of_interest)
                            <span class="badge bg-warning text-dark" title="Conflict of Interest">COI</span>
                            @else
                            <span class="badge bg-success" title="Total Score">{{ $review->total_score }}</span>
                            @endif
                        </div>
                        <div class="reviewer-date">
                            {{ optional($review->created_at)->format('d M Y, h:i A') }}
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($isAdmin && $pendingAssignments->isNotEmpty())
                    <div class="reviewer-group {{ $completedReviews->isNotEmpty() ? 'mt-2' : '' }}">
                        <span class="reviewer-group-title text-warning">
                            <i class="fa-solid fa-hourglass-half me-1"></i>Pending
                        </span>
                        @foreach($pendingAssignments as $assignment)
                        <div class="reviewer-item">
                            <span class="reviewer-name text-muted" title="{{ $assignment->assignedUser->name ?? 'Deleted User' }}">
                                {{ $assignment->assignedUser->name ?? 'Deleted User' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($completedReviews->isEmpty() && (!$isAdmin || $pendingAssignments->isEmpty()))
                    <span class="text-muted small">
                        {{ $isAdmin ? 'No reviewer assigned' : 'Not reviewed yet' }}
                    </span>
                    @endif
                </td>

                {{-- Abstract (title + category + presentation + institution) --}}
                <td class="col-abstract">
                    <div class="abstract-title mb-1" title="{{ $submission->abstract_title }}">
                        {{ \Illuminate\Support\Str::limit($submission->abstract_title, 70) }}
                    </div>

                    <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                        <span class="badge bg-light text-dark border">{{ $submission->topic_category }}</span>
                        @if(isset($presentationLabels[$submission->presentation_type]))
                        <span class="badge bg-pink">{{ $presentationLabels[$submission->presentation_type] }}</span>
                        @else
                        <span class="badge bg-secondary">{{ ucfirst($submission->presentation_type) }}</span>
                        @endif
                    </div>

                    @if($submission->institution)
                    <div class="abstract-meta">
                        <i class="fa-solid fa-hospital me-1"></i>
                        {{ \App\Helpers\MaskHelper::mask($submission->institution, 0) }}
                    </div>
                    @endif
                </td>

                {{-- Actions (sticky) --}}
                <td class="col-actions">
                    @if($isAdmin)
                    <select class="form-control select2 assign-reviewer-select mb-2"
                        name="reviewer_id[]"
                        multiple
                        data-placeholder="Assign reviewers"
                        data-route="{{ route('abstract-submission.assign-reviewer', $submission->id) }}">
                        @foreach($reviewers as $reviewer)
                        @php $hasReviewed = in_array((int) $reviewer->id, $reviewedIds, true); @endphp
                        <option value="{{ $reviewer->id }}"
                            {{ in_array((int) $reviewer->id, $assignedIds, true) ? 'selected' : '' }}
                            {{ $hasReviewed ? 'disabled' : '' }}>
                            {{ $reviewer->name }}{{ $hasReviewed ? ' (Reviewed)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @endif

                    <div class="action-btns d-flex flex-wrap gap-1 justify-content-center mt-2">
                        <a href="{{ route('abstract-submission.show', $submission->id) }}"
                            class="btn btn-sm btn-primary" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        @if($isAdmin && $completedReviews->isNotEmpty())
                        <a href="{{ route('abstract-review.score', $submission->id) }}"
                            class="btn btn-sm btn-success" title="View all reviews & scores">
                            <i class="fa-solid fa-list-check me-1"></i>Scores ({{ $completedReviews->count() }})
                        </a>
                        @elseif(!$isAdmin && $myReview)
                        <a href="{{ route('abstract-review.score', $submission->id) }}"
                            class="btn btn-sm btn-success" title="You have already submitted your review">
                            <i class="fa-solid fa-check me-1"></i>Reviewed
                        </a>
                        @else
                        <a href="{{ route('abstract-review.score', $submission->id) }}"
                            class="btn btn-sm btn-info" title="Review Abstract">
                            <i class="fa-solid fa-pen-to-square me-1"></i>Review
                        </a>
                        @endif

                        @if($isAdmin)
                        <form action="{{ route('abstract-submission.destroy', $submission->id) }}"
                            method="POST" class="d-inline m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn btn-sm btn-danger delete_abstract"
                                data-name="{{ $submission->first_name }} {{ $submission->last_name }}"
                                title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="text-muted">No abstract submissions found.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="p-3 d-flex justify-content-end">
    {{ $abstractSubmissions->links('pagination::bootstrap-5') }}
</div>