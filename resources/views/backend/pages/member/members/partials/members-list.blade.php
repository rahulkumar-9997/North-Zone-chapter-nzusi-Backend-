@if(isset($member_lists) && count($member_lists) > 0)
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="fa fa-list me-2 text-primary"></i> Members Directory
            </h5>
            <span class="badge bg-primary">{{ $member_lists->total() }} Records</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-1 ps-3">
                            <div class="d-flex align-items-center gap-2">
                                Member No
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0)" class="sort-btn text-decoration-none" data-sort="membership_no" data-order="asc">
                                        <i class="fa fa-caret-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="sort-btn text-decoration-none" data-sort="membership_no" data-order="desc">
                                        <i class="fa fa-caret-down"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="py-1">
                            <div class="d-flex align-items-center gap-2">
                                Member Details
                                <div class="d-flex flex-column">
                                    <a href="javascript:void(0)" class="sort-btn text-decoration-none" data-sort="name" data-order="asc">
                                        <i class="fa fa-caret-up"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="sort-btn text-decoration-none" data-sort="name" data-order="desc">
                                        <i class="fa fa-caret-down"></i>
                                    </a>
                                </div>
                            </div>
                        </th>
                        <th class="py-1">Contact Info</th>
                        <th class="py-1">Type</th>
                        <th class="py-1">Status</th>
                        <th class="py-1 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($member_lists as $member)
                    <tr>
                        <td class="ps-3">
                            <span class="fw-bold text-primary">{{ $member->membership_no }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="fw-bold mb-1">{{ $member->name }}</div>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($member->presentDesignations->isNotEmpty())
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                        <i class="fa fa-check-circle me-1"></i>Designation
                                    </span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                        <i class="fa fa-exclamation-circle me-1"></i>Designation Pending
                                    </span>
                                    @endif

                                    @if($member->academicQualifications->isNotEmpty())
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                        <i class="fa fa-graduation-cap me-1"></i>{{ $member->academicQualifications->count() }} Qualification
                                    </span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                        <i class="fa fa-exclamation-circle me-1"></i>Academic Pending
                                    </span>
                                    @endif

                                    @if($member->trainings->isNotEmpty())
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                        <i class="fa fa-hospital-o me-1"></i>{{ $member->trainings->count() }} Training
                                    </span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                        <i class="fa fa-exclamation-circle me-1"></i>Training Pending
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small class="text-muted mb-1">
                                    <i class="fa fa-envelope me-1"></i> {{ $member->email }}
                                </small>
                                <small>
                                    <i class="fa fa-phone me-1"></i> {{ $member->mobile_no ?? 'Not provided' }}
                                </small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                {{ $member->type->title ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            @if($member->status == 'approved')
                            <span class="badge bg-success px-3 py-2">
                                <i class="fa fa-thumbs-up me-1"></i> Approved
                            </span>
                            @elseif($member->status == 'pending')
                            <span class="badge bg-warning px-3 py-2">
                                <i class="fa fa-hourglass-half me-1"></i> Pending
                            </span>
                            @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fa fa-times-circle me-1"></i> Rejected
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a class="btn btn-sm btn-outline-primary rounded"
                                    href="{{ route('manage-member.edit', $member->id) }}"
                                    title="Edit Member">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-info rounded"
                                    href="{{ route('manage-member.show', $member->id) }}"
                                    title="View Member">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <form action="{{ route('manage-member.destroy', $member->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-danger rounded show_confirm"
                                        title="Delete Member">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <div class="mt-3">
            {{ $member_lists->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@else
<div class="text-center py-5">
    <div class="mb-4">
        <div class="bg-light rounded-circle d-inline-flex p-4">
            <i class="fa fa-users fa-4x text-muted"></i>
        </div>
    </div>
    <h4 class="mb-2">No Members Found</h4>
    <p class="text-muted mb-4">Get started by creating your first member.</p>
    <a href="{{ route('manage-member.create') }}" class="btn btn-primary px-4">
        <i class="fa fa-plus me-2"></i> Add New Member
    </a>
</div>
@endif