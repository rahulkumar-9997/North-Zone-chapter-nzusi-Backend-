<table class="table table-bordered table-striped">
     <thead>
         <tr>
             <th>Sr. No.</th>
             <th>Name</th>
             <th>Email</th>
             <th>Phone</th>
             <th>Presentation Type</th>
             <th>Abstract Title</th>
             <!-- <th>Status</th> -->
             <th>File</th>
             <th>Date</th>
             <th>Actions</th>
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
                <br><span class="badge bg-primary">
                <strong>NZUSI:</strong> {{ $submission->nzusi_membership_no }}
                </span>
                @endif

                @if ($submission->usi_membership_no)
                <br><span class="badge bg-secondary">
                    <strong>USI:</strong> {{ $submission->usi_membership_no }}
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
                @if($submission->presentation_type =='video')
                    Video Presentation (BV)
                @elseif($submission->presentation_type =='podium')
                    Podium / Best Paper (BP)
                @elseif($submission->presentation_type =='poster')
                    Moderated Poster (BPos)
                @elseif($submission->presentation_type =='eposter')
                    Unmoderated e-Poster (UPos)
                @else
                    {{ $submission->presentation_type }}
                @endif
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
             <td>
                 <a href="{{ route('abstract-submission.show', $submission->id) }}" class="btn btn-sm btn-primary">
                     <i class="fa-solid fa-eye"></i>
                 </a>
                 <form action="{{ route('abstract-submission.destroy', $submission->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger delete_abstract" data-name="{{ $submission->first_name }} {{ $submission->last_name }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
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