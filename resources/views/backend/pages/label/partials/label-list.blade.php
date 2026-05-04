@if(isset($labels) && count($labels) > 0)
<table class="table align-middle mb-0 table-hover table-centered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($labels as $label)
        <tr>
            <td>
                {{ $label->title }}
            </td>           
            
            <td>
                @if($label->status == '1')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-warning">Inactive</span>               
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">                   
                    <a class="me-2 p-2" href="javascript:void(0);"
                    data-route="{{ route('label.edit', $label) }}"
                    data-size="lg"
                    data-title="Edit Label"
                    data-label-edit="true">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('label.destroy', $label) }}" method="POST" class="d-inline">
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
    <h4 class="mb-2">No Label Found</h4>
</div>
@endif