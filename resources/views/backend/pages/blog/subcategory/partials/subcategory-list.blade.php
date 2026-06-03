@if(isset($subcategories) && count($subcategories) > 0)
<table class="table align-middle mb-0 table-hover table-centered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Images</th>
            <th>Short Content</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subcategories as $subcategory)
        <tr>
            <td>
                {{ $subcategory->title }}
                <br>
                <small class="text-muted">Category: {{ $subcategory->category ? $subcategory->category->title : 'N/A' }}</small>
                
            </td>
            <td>
               @if($subcategory->image)
                <img src="{{ asset('storage/images/blog-subcategory/small/' . $subcategory->image) }}"
                    class="img-thumbnail"
                    style="width:50px;height:50px;object-fit:cover;"
                    alt="{{ $subcategory->title }}">
                @else
                <span class="text-muted">No Image</span>
                @endif                    
            </td>
            <td>
                 {{ Str::limit($subcategory->short_content, 50) }}
            </td>
             <td>
                @if($subcategory->status == '1')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-warning">Inactive</span>               
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">                   
                    <a class="me-2 p-2" href="javascript:void(0);"
                    data-route="{{ route('blog-subcategory.edit', $subcategory) }}"
                    data-size="lg"
                    data-title="Edit Blog Subcategory"
                    data-blog-subcategory-edit="true"
                    data-toggle="tooltip"
                    title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('blog-subcategory.destroy', $subcategory) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger show_confirm" data-toggle="tooltip" title='Delete'>
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
    <h4 class="mb-2">No Blog Subcategory Found</h4>
    <p class="mb-0">Start creating your first blog subcategory.</p>
</div>
@endif