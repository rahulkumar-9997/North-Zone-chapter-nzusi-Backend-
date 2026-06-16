<div class="table-responsive">
    <table class="table table-hover align-middle table-bordered">
        <thead class="table-dark">
            <tr>
                <th width="60">#</th>
                <th width="260">Participant Details</th>
                <th width="180">Contact</th>
                <th width="180">Status</th>
                <th width="180">Category/ Presentation</th>
                <th width="180">Abstract Title</th>
                <th width="120">File</th>
                <th width="120">Date</th>
                <th width="120" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($abstractSubmissions as $submission)
            <tr>
                <td class="fw-bold text-center">
                    {{ $loop->iteration + ($abstractSubmissions->currentPage() - 1) * $abstractSubmissions->perPage() }}
                </td>
                <td>
                    <div class="fw-semibold text-dark">
                        {{ $submission->first_name }}
                        {{ $submission->last_name }}
                    </div>
                    @if ($submission->abstract_id)
                    <div class="mt-1">
                    <span class="badge bg-dark"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Unique Abstract Submission ID">
                        <i class="fa-solid fa-id-badge me-1"></i>
                        {{ $submission->abstract_id }}
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
                        {{ $submission->nzusi_membership_no }}
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
                        {{ $submission->usi_membership_no }}
                    </span>
                    </div>
                    @endif
                </td>
                <td>
                    @if($submission->phone)
                    <div class="mb-1">
                        <a href="tel:{{ $submission->phone }}"
                            class="text-decoration-none">
                            {{ $submission->phone }}
                        </a>
                    </div>
                    @endif
                    @if($submission->email)
                    <div>
                        <a href="mailto:{{ $submission->email }}"
                            class="text-decoration-none">
                            {{ $submission->email }}
                        </a>
                    </div>
                    @endif
                </td>
               
                <td class="text-center">
                    <button class="btn btn-sm border-0 p-0 open-review-modal status-btn"
                        data-status="{{ $submission->status }}"
                        data-title="{{ $submission->first_name }}"
                        data-size="lg"
                        data-route="{{ route('abstract-review.create', $submission->id) }}"
                        data-abstract="true"
                        title="Click to update status">
                        @if($submission->status == 'pending')
                            <span class="badge bg-warning status-badge">
                                <i class="fa-solid fa-clock me-1"></i>
                                Pending
                                <i class="fa-solid fa-pen-to-square ms-1 opacity-75"></i>
                            </span>
                        @elseif($submission->status == 'approved')
                            <span class="badge bg-success status-badge">
                                <i class="fa-solid fa-check me-1"></i>
                                Approved
                                <i class="fa-solid fa-pen-to-square ms-1 opacity-75"></i>
                            </span>

                        @else
                            <span class="badge bg-danger status-badge">
                                <i class="fa-solid fa-xmark me-1"></i>
                                Rejected
                                <i class="fa-solid fa-pen-to-square ms-1 opacity-75"></i>
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
                        {{ $submission->abstract_title }}
                    </div>

                    @if($submission->institution)
                        <small class="text-muted">
                            <strong>Institution / Hospital :</strong>
                            {{ $submission->institution }}
                        </small>
                    @endif
                </td>
                <td class="text-center">
                    @if($submission->supporting_file)
                    <a href="{{ asset('storage/images/abstract-submission/' . $submission->supporting_file) }}"
                        target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-file-pdf"></i>
                        View
                    </a>
                    @else
                    <span class="text-muted">
                        N/A
                    </span>
                    @endif
                </td>
                <td>
                    <div>
                        {{ $submission->created_at->format('d M Y h:i A') }}
                    </div>
                </td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <a href="{{ route('abstract-submission.show', $submission->id) }}"
                            class="btn btn-sm btn-primary"
                            title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @php
                            $user = auth()->user();
                        @endphp

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