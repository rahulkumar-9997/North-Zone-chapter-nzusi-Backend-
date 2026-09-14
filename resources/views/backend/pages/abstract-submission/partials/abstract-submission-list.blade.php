<div class="table-responsive">
    <table class="table table-hover align-middle table-bordered">
        <thead class="table-dark">
            <tr>
                <th width="60">#</th>
                <th width="260">Participant Details</th>
                <th width="180">Contact</th>
                <th width="130">Status</th>
                <th width="150">Category/ Presentation</th>
                <th width="180">Abstract Title</th>
                <th width="120" class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            <!-- {{ auth()->user()->role_names }} -->
            @forelse($abstractSubmissions as $submission)
            <tr>
                <td class="fw-bold text-center">
                    {{ $loop->iteration + ($abstractSubmissions->currentPage() - 1) * $abstractSubmissions->perPage() }}
                </td>
                <td>
                    <div class="fw-semibold text-dark">
                        {{ \App\Helpers\MaskHelper::mask($submission->first_name, 0) }}
                        {{ \App\Helpers\MaskHelper::mask($submission->last_name, 0) }}
                    </div>
                    @if ($submission->abstract_id)
                    <div class="mt-1">
                    <span class="badge bg-dark"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Unique Abstract Submission ID">
                        <i class="fa-solid fa-id-badge me-1"></i>
                       {{ \App\Helpers\MaskHelper::maskLimit($submission->abstract_id, 20, 0) }}
                    </span>
                    </div>
                    @endif
                    @if ($submission->nzusi_membership_no)
                    <div class="mt-1">
                    <span class="badge bg-primary"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="NZUSI Membership Number">
                        <i class="fa-solid fa-user-check me-1"></i>
                        NZUSI:
                        {{ \App\Helpers\MaskHelper::maskLimit($submission->nzusi_membership_no, 20, 0) }}
                    </span>
                    </div>
                    @endif
                    @if ($submission->usi_membership_no)
                    <div class="mt-1"> 
                    <span class="badge bg-secondary"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="USI Membership Number">
                        <i class="fa-solid fa-users me-1"></i>
                        USI:
                        {{ \App\Helpers\MaskHelper::maskLimit($submission->usi_membership_no, 20, 0) }}
                    </span>
                    </div>
                    @endif
                    <div>
                        @if($submission->supporting_file)
                        <a href="{{ asset('storage/images/abstract-submission/' . $submission->supporting_file) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-file-pdf"></i>
                            View
                        </a>
                        </span>
                        @endif
                    </div>
                </td>
                <td>
                    @if($submission->phone)
                    <div class="mb-1">
                        <a href="tel:{{ \App\Helpers\MaskHelper::mask($submission->phone, 0) }}"
                            class="text-decoration-none">
                            {{ \App\Helpers\MaskHelper::mask($submission->phone, 0) }}
                        </a>
                    </div>
                    @endif
                    @if($submission->email)
                    <div>
                        <a href="mailto:{{ \App\Helpers\MaskHelper::mask($submission->email, 0) }}"
                            class="text-decoration-none">
                            {{ \App\Helpers\MaskHelper::mask($submission->email, 0) }}
                        </a>
                    </div>
                    @endif                
                    
                    <div class="text-idn_to_utf8">
                        {{ $submission->created_at->format('d M Y h:i A') }}
                    </div>
                </td>
               
                <td class="text-center">
                    <button class="btn btn-sm border-0 p-0 open-review-modal status-btn"
                        data-status="{{ $submission->status }}"
                        data-title="{{ $submission->first_name }}"
                        data-size="xl"
                        data-route="{{ route('abstract-review.show', $submission->id) }}"
                        data-abstract="true"
                        title="Click to update status">
                        @if($submission->status == 'pending')
                            <span class="badge bg-warning status-badge">
                                <i class="fa-solid fa-clock me-1"></i>
                                Pending
                            </span>
                        @elseif($submission->status == 'approved')
                            <span class="badge bg-success status-badge">
                                <i class="fa-solid fa-check me-1"></i>
                                Reviewed                               
                            </span>
                        @else
                            <span class="badge bg-danger status-badge">
                                <i class="fa-solid fa-xmark me-1"></i>
                                Rejected
                            </span>
                        @endif
                    </button>
                </td>
                <td>
                    <span class="fw-medium">
                        {{ $submission->topic_category }}
                    </span><br>
                    @if($submission->presentation_type == 'video')
                    <span class="badge bg-pink">
                        Video Presentation (BV)
                    </span>
                    @elseif($submission->presentation_type == 'podium')
                    <span class="badge bg-pink">
                        Podium / Best Paper (BP)
                    </span>
                    @elseif($submission->presentation_type == 'poster')
                    <span class="badge bg-pink">
                        Moderated Poster (BPos)
                    </span>
                    @elseif($submission->presentation_type == 'eposter')
                    <span class="badge bg-pink">
                        Unmoderated e-Poster (UPos)
                    </span>
                    @else
                    <span class="badge bg-secondary">
                        {{ ucfirst($submission->presentation_type) }}
                    </span>
                    @endif
                </td>
                <td class="abstract-title-column">
                    <div class="fw-semibold text-dark">
                       {{ Str::limit($submission->abstract_title, 50) }}
                    </div>

                    @if($submission->institution)
                        <small class="text-muted">
                            <strong>Institution / Hospital :</strong><br>
                            {{ \App\Helpers\MaskHelper::mask($submission->institution , 0) }}
                        </small>
                    @endif
                </td>
                
                <td class="text-center">
                    <div class="gap-1 justify-content-center">
                        @php
                            $user = auth()->user();
                        @endphp
                        <!-- {{ $submission->status == 'approved' ? 'disabled' : '' }} -->
                        @if($user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']))
                            @php
                                $assignedIds = $submission->assignments->pluck('assigned_to')->toArray();
                            @endphp
                            <div class="reviewer-select-wrapper">
                                <select class="form-control select2 assign-reviewer-select"
                                    name="reviewer_id[]"
                                    multiple
                                    data-route="{{ route('abstract-submission.assign-reviewer', $submission->id) }}"
                                    title="{{ $submission->status == 'approved' ? 'Cannot reassign, abstract already approved' : '' }}">
                                    @foreach($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}"
                                            {{ in_array($reviewer->id, $assignedIds) ? 'selected' : '' }}>
                                            {{ $reviewer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <a href="{{ route('abstract-submission.show', $submission->id) }}"
                            class="btn btn-sm btn-primary"
                            title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if($submission->reviews->isNotEmpty())
                            <a href="{{ route('abstract-review.score', $submission->id) }}"
                                class="btn btn-sm btn-success"
                                title="You have already submitted your review for this abstract">
                                <i class="fa-solid fa-check me-1"></i>
                                Reviewed
                            </a>
                        @else
                            <a href="{{ route('abstract-review.score', $submission->id) }}"
                                class="btn btn-sm btn-info"
                                title="Review Abstract">
                                Review Abstract
                            </a>
                        @endif
                        @if($user->is_admin == 1 || $user->hasAnyRole(['webadmin', 'admin']))
                            <form action="{{ route('abstract-submission.destroy', $submission->id) }}"
                                method="POST"
                                class="d-inline">
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
                <td colspan="9" class="text-center py-4">
                    <div class="text-muted">
                        No abstract submissions found.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3 d-flex justify-content-end">
    {{ $abstractSubmissions->links('pagination::bootstrap-5') }}
</div>