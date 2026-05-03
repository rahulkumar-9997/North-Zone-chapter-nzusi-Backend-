@if(isset($memberTypes) && count($memberTypes) > 0)
<table class="table align-middle mb-0 table-hover table-centered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($memberTypes as $memberType)
        <tr>
            <td>
                {{ $memberType->title }}
            </td>           
            
            <td>
                @if($memberType->status == '1')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-warning">Inactive</span>               
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">                   
                    <a class="me-2 p-2" href="javascript:void(0);"
                    data-route="{{ route('member-type.edit', $memberType) }}"
                    data-size="lg"
                    data-title="Edit Member Type"
                    data-member-type-edit="true">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('member-type.destroy', $memberType) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger show_confirm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>

                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="text-center p-4">
    <h4 class="mb-2">No Member Type List Found</h4>
    <p class="mb-0">Start creating your first Member type.</p>
</div>
@endif