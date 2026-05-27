 <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Institution</th>
            <th>Presentation Type</th>
            <th>Abstract Title</th>
            <!-- <th>Status</th> -->
            <th>File</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($abstractSubmissions as $submission)
        <tr>
            <td>
                {{ $loop->iteration + ($abstractSubmissions->currentPage() - 1) * $abstractSubmissions->perPage() }}
            </td>
            <td>
                {{ $submission->first_name }}
                {{ $submission->last_name }}
                @if ($submission->nzusi_membership_no)
                   <span class="badge bg-primary">
                       {{ $submission->nzusi_membership_no }}
                   </span>
                @endif

                @if ($submission->usi_membership_no)
                   <span class="badge bg-secondary">
                       {{ $submission->usi_membership_no }}
                   </span>
                @endif

            </td>
            <td>
                {{ $submission->email }}
            </td>
            <td>
                {{ $submission->phone }}
            </td>
            <td>
                {{ $submission->institution }}
            </td>
            <td>
                {{ $submission->presentation_type }}
            </td>
            <td>
                {{ $submission->abstract_title }}
            </td>
            <!-- <td>
                @if($submission->status == 'pending')
                <span class="badge bg-warning">
                    Pending
                </span>
                @elseif($submission->status == 'approved')
                <span class="badge bg-success">
                    Approved
                </span>
                @else
                <span class="badge bg-danger">
                    Rejected
                </span>
                @endif
            </td> -->
            <td>
            @if($submission->supporting_file)
                <a
                    href="{{ asset('storage/images/abstract-submission/' . $submission->supporting_file) }}"
                    target="_blank"
                    class="btn btn-sm btn-info">
                    View File
                </a>
                @else
                N/A
            @endif
            </td>
            <td>
                {{ $submission->created_at->format('d M Y') }}
            </td>
         </tr>
         @empty
         <tr>
             <td colspan="10" class="text-center">
                 No records found.
             </td>
         </tr>
        @endforelse
    </tbody>
 </table>
 <div class="mt-3 mb-2">
    {{ $abstractSubmissions->links('pagination::bootstrap-5') }}
</div>