<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Icon</th>
                <th>Menu Name</th>
                <th>Slug</th>
                <th>Route/URL</th>
                <th>Parent</th>
                <th>Order</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($menus as $menu)
            <tr>
                <td>{{ $menu->id }}</td>
                <td><i class="{{ $menu->icon }}"></i></td>
                <td>
                    <strong>{{ $menu->name }}</strong>
                    @if($menu->children->count() > 0)
                    <span class="badge badge-info ml-2">{{ $menu->children->count() }} children</span>
                    @endif
                </td>
                <td><code>{{ $menu->slug }}</code></td>
                <td>
                    @if($menu->route)
                    <span class="text-success">{{ $menu->route }}</span>
                    @elseif($menu->url)
                    <span class="text-info">{{ $menu->url }}</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    @if($menu->parent)
                    {{ $menu->parent->name }}
                    @else
                    <span class="text-muted">Main Menu</span>
                    @endif
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm order-input"
                        data-id="{{ $menu->id }}" value="{{ $menu->order }}"
                        style="width: 70px;">
                </td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input status-toggle"
                            id="status_{{ $menu->id }}" data-id="{{ $menu->id }}"
                            {{ $menu->status ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_{{ $menu->id }}">
                            <span class="badge badge-{{ $menu->status ? 'success' : 'danger' }}">
                                {{ $menu->status ? 'Active' : 'Inactive' }}
                            </span>
                        </label>
                    </div>
                </td>
                <td>
                    <a href="{{ route('menus.edit', $menu) }}" class="btn btn-sm btn-warning">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>

            @foreach($menu->children as $child)
            <tr style="background-color: #f9f9f9;">
                <td>{{ $child->id }}</td>
                <td><i class="{{ $child->icon }}"></i></td>
                <td>
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="ti ti-corner-down-right"></i>
                    {{ $child->name }}
                </td>
                <td><code>{{ $child->slug }}</code></td>
                <td>
                    @if($child->route)
                    <span class="text-success">{{ $child->route }}</span>
                    @elseif($child->url)
                    <span class="text-info">{{ $child->url }}</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $menu->name }}</td>
                <td>
                    <input type="number" class="form-control form-control-sm order-input"
                        data-id="{{ $child->id }}" value="{{ $child->order }}"
                        style="width: 70px;">
                </td>
                <td>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input status-toggle"
                            id="status_{{ $child->id }}" data-id="{{ $child->id }}"
                            {{ $child->status ? 'checked' : '' }}>
                        <label class="custom-control-label" for="status_{{ $child->id }}">
                            <span class="badge badge-{{ $child->status ? 'success' : 'danger' }}">
                                {{ $child->status ? 'Active' : 'Inactive' }}
                            </span>
                        </label>
                    </div>
                </td>
                <td>
                    <a href="{{ route('menus.edit', $child) }}" class="btn btn-sm btn-warning">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <form action="{{ route('menus.destroy', $child) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            @empty
            <tr>
                <td colspan="9" class="text-center">No menus found</td>
            </tr>
            @endforelse
        </tbody>
    </table>