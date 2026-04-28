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
        @foreach($blogCategories as $blogCategory)
        <tr>
            <td>
                {{ $blogCategory->title }}
            </td>
            <td>
               @if($blogCategory->image)
                <img src="{{ asset('storage/images/blog-category/small/' . $blogCategory->image) }}"
                    class="img-thumbnail"
                    style="width:50px;height:50px;object-fit:cover;"
                    alt="{{ $blogCategory->title }}">
                @else
                <span class="text-muted">No Image</span>
                @endif                    
            </td>
            <td>
                 {{ Str::limit($blogCategory->short_content, 50) }}
            </td>
             <td>
                @if($blogCategory->status == '1')
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-warning">Inactive</span>               
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">                   
                    <a class="me-2 p-2" href="javascript:void(0);"
                    data-route="{{ route('blog-category.edit', $blogCategory) }}"
                    data-size="lg"
                    data-title="Edit Blog Category"
                    data-blog-category-edit="true">
                        <i class="fa fa-edit"></i>
                    </a>
                    <form action="{{ route('blog-category.destroy', $blogCategory) }}" method="POST" class="d-inline">
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