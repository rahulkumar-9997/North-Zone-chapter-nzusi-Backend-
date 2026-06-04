<table class="table table-bordered table-striped align-middle">
    <thead>
        <tr>
            <th width="60">ID</th>
            <th width="80">Icon</th>
            <th>Menu Name</th>
            <th>Slug</th>
            <th>Route / URL</th>
            <th>Parent</th>
            <th width="180">Order</th>
            <th width="120">Status</th>
            <th width="120">Sidebar Status</th>
            <th width="180">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($menus as $index => $menu)
        <tr>
            <td>{{ $menu->id }}</td>
            <td>
                @if($menu->icon)
                <i class="{{ $menu->icon }}"></i>
                @else
                -
                @endif
            </td>
            <td>
                <strong>{{ $menu->name }}</strong>
                @if($menu->children->count())
                <span class="badge bg-info ms-2">
                    {{ $menu->children->count() }} Child
                </span>
                @endif
            </td>
            <td>
                <code>{{ $menu->slug }}</code>
            </td>
            <td>
                @if($menu->route)
                <span class="badge bg-success">
                    {{ $menu->route }}
                </span>
                @elseif($menu->url)
                <span class="badge bg-primary">
                    {{ $menu->url }}
                </span>
                @else
                -
                @endif
            </td>
            <td>
                <span class="badge bg-secondary">
                    Main Menu
                </span>
            </td>
            <td>
                <div class="d-flex align-items-center gap-1">
                    @if($index > 0)
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary order-btn"
                        data-id="{{ $menu->id }}"
                        data-direction="up">
                        <i class="ti ti-arrow-up"></i>
                    </button>
                    @endif
                    <span class="badge bg-primary px-3 py-2">
                        {{ $menu->order }}
                    </span>
                    @if($index < ($menus->count() - 1))
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary order-btn"
                            data-id="{{ $menu->id }}"
                            data-direction="down">
                            <i class="ti ti-arrow-down"></i>
                        </button>
                        @endif
                </div>
            </td>
            {{-- STATUS --}}
            <td>
                <div class="form-check form-switch">
                    <input
                        class="form-check-input status-toggle"
                        type="checkbox"
                        data-id="{{ $menu->id }}"
                        id="status_{{ $menu->id }}"
                        {{ $menu->status ? 'checked' : '' }}>
                    <label
                        class="form-check-label"
                        for="status_{{ $menu->id }}">
                        {{ $menu->status ? 'Active' : 'Inactive' }}

                    </label>
                </div>
            </td>
            {{-- SIDEBAR STATUS --}}
            <td>
                <div class="form-check form-switch">
                    <input
                        class="form-check-input sidebar-toggle"
                        type="checkbox"
                        data-id="{{ $menu->id }}"
                        id="sidebar_{{ $menu->id }}"
                        {{ $menu->sidebar_status ? 'checked' : '' }}>
                    <label
                        class="form-check-label"
                        for="sidebar_{{ $menu->id }}">
                        {{ $menu->sidebar_status ? 'Show' : 'Hide' }}
                    </label>
                </div>
            </td>
            {{-- ACTION --}}
            <td>
                <a href="{{ route('menus.edit',$menu->id) }}"
                    class="btn btn-warning btn-sm">
                    <i class="ti ti-edit"></i>
                </a>
                <form
                    action="{{ route('menus.destroy',$menu->id) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm show_confirm_menu"
                        data-name="{{ $menu->name }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        {{-- CHILD MENUS --}}
        @foreach($menu->children as $childIndex => $child)
        <tr style="background-color:#f8f9fa">
            <td>{{ $child->id }}</td>
            <td>
                @if($child->icon)
                <i class="{{ $child->icon }}"></i>
                @else
                -
                @endif
            </td>
            <td>
                <span class="ms-4">
                    <i class="ti ti-corner-down-right"></i>
                    {{ $child->name }}
                </span>
            </td>
            <td>
                <code>{{ $child->slug }}</code>
            </td>
            <td>
                @if($child->route)
                <span class="badge bg-success">
                    {{ $child->route }}
                </span>
                @elseif($child->url)
                <span class="badge bg-primary">
                    {{ $child->url }}
                </span>
                @else
                -
                @endif
            </td>
            <td>{{ $menu->name }}</td>
            {{-- CHILD ORDER --}}
            <td>
                <div class="d-flex align-items-center gap-1">
                    @if($childIndex > 0)
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary order-btn"
                        data-id="{{ $child->id }}"
                        data-direction="up">
                        <i class="ti ti-arrow-up"></i>
                    </button>
                    @endif
                    <span class="badge bg-primary px-3 py-2">
                        {{ $child->order }}
                    </span>
                    @if($childIndex < ($menu->children->count() - 1))
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary order-btn"
                            data-id="{{ $child->id }}"
                            data-direction="down">
                            <i class="ti ti-arrow-down"></i>
                        </button>
                        @endif
                </div>
            </td>
            {{-- CHILD STATUS --}}
            <td>
                <div class="form-check form-switch">
                    <input
                        class="form-check-input status-toggle"
                        type="checkbox"
                        data-id="{{ $child->id }}"
                        id="status_{{ $child->id }}"
                        {{ $child->status ? 'checked' : '' }}>
                    <label
                        class="form-check-label"
                        for="status_{{ $child->id }}">
                        {{ $child->status ? 'Active' : 'Inactive' }}
                    </label>
                </div>
            </td>
            {{-- CHILD SIDEBAR STATUS --}}
            <td>
                <div class="form-check form-switch">
                    <input
                        class="form-check-input sidebar-toggle"
                        type="checkbox"
                        data-id="{{ $child->id }}"
                        id="sidebar_{{ $child->id }}"
                        {{ $child->sidebar_status ? 'checked' : '' }}>
                    <label
                        class="form-check-label"
                        for="sidebar_{{ $child->id }}">
                        {{ $child->sidebar_status ? 'Show' : 'Hide' }}
                    </label>
                </div>
            </td>
            {{-- CHILD ACTION --}}
            <td>
                <a href="{{ route('menus.edit',$child->id) }}"
                    class="btn btn-warning btn-sm">
                    <i class="ti ti-edit"></i>
                </a>
                <form
                    action="{{ route('menus.destroy',$child->id) }}"
                    method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm show_confirm_menu"
                        data-name="{{ $child->name }}">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
        @empty
        <tr>
            <td colspan="9" class="text-center">
                No Menus Found
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@if(method_exists($menus, 'links'))
<div class="mt-3">
    {{ $menus->links() }}
</div>
@endif