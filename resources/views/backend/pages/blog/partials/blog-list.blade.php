<table class="table align-middle mb-0 table-hover table-centered">
    <thead>
        <tr>
            <th>#</th>           
            <th>Title</th>
            <th>Main Image</th>
            <th>User</th>
            <th>Category</th>
            <th>Status</th>
            <th>More Images</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($blogs as $key => $blog)
        <tr>
            <td>{{ $blogs->firstItem() + $key }}</td>
            <td>
                {{ Str::limit($blog->title, 50) }}
                @if($blog->pdf_file)
                    <br><a href="{{ asset('storage/pdf/blog/'.$blog->pdf_file) }}" target="_blank">View PDF</a>
                @endif
            </td>
            <td>
                @if($blog->image_file)
                    <img class="img-thumbnail" src="{{ asset('storage/images/blog/'.$blog->image_file) }}" width="60" style="width:70px;height:70px;object-fit:cover;">
                @else
                N/A
                @endif
            </td>
            <td>
                {{ $blog->user->name }}
            </td>
            <td>{{ $blog->category->title ?? 'N/A' }}</td>
            <td>
                @if($blog->status)
                <span class="badge bg-success">Active</span>
                @else
                <span class="badge bg-danger">Inactive</span>
                @endif
            </td>
            <td>
                @if($blog->images->count())
                    <span class="badge bg-info">
                        {{ $blog->images->count() }} Images
                    </span>
                @else
                    N/A
                @endif
            </td>
            <td class="action-table-data">
                <div class="edit-delete-action">         
                    <a href="{{ route('blog-post.edit', $blog->id) }}" class="me-2 p-2">
                        <i class="fa fa-edit"></i>
                    </a>                    
                    <form action="{{ route('blog-post.destroy', $blog->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger show_confirm_blog">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                    
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">No Data Found</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-2 mb-3">
    {{ $blogs->links('pagination::bootstrap-5') }}
</div>