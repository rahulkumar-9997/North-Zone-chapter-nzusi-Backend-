<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Slug</th>
                <th>Users Count</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td><strong>{{ $role->name }}</strong></td>
                <td><code>{{ $role->slug }}</code></td>
                <td>
                    <span class="badge badge-info">{{ $role->users_count }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ $role->is_active ? 'success' : 'danger' }}">
                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $role->created_at->format('d-m-Y') }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger show_confirm_role" type="submit" data-name="{{ $role->name }}">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No roles found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $roles->links() }}