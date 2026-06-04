@php
    $indent = $level * 20;
    $hasChildren = $menu->children->count() > 0;
    $isParent = $hasChildren;
    $isChecked = false;
    if(isset($selectedMenus) && is_array($selectedMenus)) {
        $isChecked = in_array($menu->id, $selectedMenus);
    } else {
        $isChecked = in_array($menu->id, old('menus', []));
    }
    $hasCheckedChildren = false;
    if($hasChildren && isset($selectedMenus) && is_array($selectedMenus)) {
        foreach($menu->children as $child) {
            if(in_array($child->id, $selectedMenus)) {
                $hasCheckedChildren = true;
                break;
            }
        }
    }
    if(!$hasCheckedChildren && $hasChildren && !isset($selectedMenus)) {
        foreach($menu->children as $child) {
            if(in_array($child->id, old('menus', []))) {
                $hasCheckedChildren = true;
                break;
            }
        }
    }
@endphp
<div class="tree-item-content" style="margin-left: {{ $indent }}px;">
    <div class="d-flex align-items-center">
        @if($hasChildren)
            <span class="expand-icon">
                <i class="ti ti-chevron-right"></i>
            </span>
        @else
            <span style="width: 28px;"></span>
        @endif

        <div class="form-check">
            <input type="checkbox"
                class="form-check-input {{ $isParent ? 'parent-checkbox' : 'child-checkbox' }}"
                id="menu_{{ $menu->id }}"
                name="menus[]"
                value="{{ $menu->id }}"
                data-parent-id="{{ $menu->id }}"
                {{ $isChecked ? 'checked' : '' }}>
            <label class="form-check-label" for="menu_{{ $menu->id }}">
                <strong>{{ $menu->name }}</strong>
                
                @if($hasChildren)
                    <span class="badge badge-info badge-count">{{ $menu->children->count() }} submenus</span>
                @endif
            </label>
        </div>
    </div>
</div>

@if($hasChildren)
    <div class="tree-node-children">
        @foreach($menu->children as $child)
            <div class="tree-node">
                @include('backend.pages.roles.partials.menu-tree-item', [
                    'menu' => $child,
                    'level' => $level + 1,
                    'selectedMenus' => $selectedMenus ?? []
                ])
            </div>
        @endforeach
    </div>
@endif