@if(isset($member_lists) && count($member_lists) > 0)
<table class="table align-middle mb-0 table-hover table-centered">
    <thead>
        <tr>
            <th>Membership No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($member_lists as $member)
        <tr>
            <td>{{ $member->membership_no }}</td>
            <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
            <td>{{ $member->mobile_no }}</td>
            <td>
                {{ $member->type->title ?? '-' }}
            </td>
            <td>
                @if($member->status == 'approved')
                <span class="badge bg-success">Approved</span>
                @elseif($member->status == 'pending')
                <span class="badge bg-warning">Pending</span>
                @else
                <span class="badge bg-danger">Rejected</span>
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">
                    <a class="me-2 p-2"
                        href="{{ route('manage-member.edit', $member->id) }}">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('manage-member.destroy', $member->id) }}"
                        method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="btn btn-sm btn-danger show_confirm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-2 mb-3">
    {{ $member_lists->links('pagination::bootstrap-5') }}
</div>
@else
<div class="text-center p-4">
    <h4 class="mb-2">No Members Found</h4>
    <p class="mb-0">Start adding your first Member.</p>
</div>
@endif