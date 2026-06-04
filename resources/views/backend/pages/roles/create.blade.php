@extends('backend.layouts.master')
@section('title','Create Role')
@push('styles')
<style>
    .menu-tree {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 5px 20px;
    }
    
    .tree-item {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    
    .tree-node {
        position: relative;
        padding-left: 0;
    }
    
    .tree-node .tree-item-content {
        padding: 8px 12px;
        margin: 2px 0;
        border-radius: 0.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }
   
    
    .tree-node .tree-item-content .form-check {
        margin-bottom: 0;
    }
    
    .tree-node-children {
        padding-left: 30px;
        position: relative;
    }
    
    .tree-node-children:before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #dee2e6;
    }
    
    .tree-node-children .tree-node:last-child:before {
        height: 50%;
    }
    
    .expand-icon {
        cursor: pointer;
        margin-right: 8px;
        font-size: 14px;
        display: inline-block;
        width: 20px;
        color: #6c757d;
        transition: transform 0.2s;
    }
    
    .expand-icon.expanded {
        transform: rotate(90deg);
    }
    
    .menu-icon {
        margin-right: 8px;
        width: 20px;
        display: inline-block;
    }
    
    .badge-count {
        font-size: 10px;
        margin-left: 8px;
    }
    
    .select-all-btn {
        margin-bottom: 1rem;
        padding: 0.5rem;
        background: #f8f9fa;
        border-radius: 0.25rem;
    }
    
    .menu-tree-container {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background: white;
    }
    
    .menu-tree-header {
        background: #f8f9fa;
        padding: 5px 20px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0.5rem 0.5rem 0 0;
    }
</style>
@endpush
@section('main-content')
<div class="content">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
            <h4 class="card-title">Create Role</h4>
            <a href="{{ route('roles.index') }}"
                data-title="Create Role"
                class="btn btn-primary">
                <i data-feather="arrow-left" class="me-2"></i> Back to Role List
            </a>
        </div>
        <div class="card-body">
            <form action="{{ isset($role) ? route('roles.update',$role->id) : route('roles.store') }}"
                method="POST">
                @csrf
                @if(isset($role))
                @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name',$role->name ?? '') }}"
                                placeholder="Admin, Editor, Viewer">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">
                                Slug
                            </label>
                            <input type="text"
                                name="slug"
                                id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug',$role->slug ?? '') }}"
                                placeholder="admin">
                            <small class="text-muted">
                                Leave blank for auto generate
                            </small>
                            @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">
                                Status
                            </label>
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', $role->is_active ?? 1) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="menu-tree-container">
                            <div class="menu-tree-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        Assign Menus to Role
                                    </h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-success" id="selectAllMenus">
                                            <i class="ti ti-check-all"></i> Select All
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" id="deselectAllMenus">
                                            <i class="ti ti-x"></i> Deselect All
                                        </button>
                                    </div>
                                </div>
                                <span class="text-danger small mt-2 mb-0">
                                    Select the menus that users with this role will be able to access
                                </span>
                            </div>
                            
                            <div class="menu-tree">
                                @php
                                    $selectedMenus = isset($roleMenus) ? $roleMenus : old('menus', []);
                                @endphp
                                @foreach($menus as $menu)
                                    <div class="tree-node">
                                        @include('backend.pages.roles.partials.menu-tree-item', ['menu' => $menu, 'level' => 0, 'selectedMenus' => $selectedMenus])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <a href="{{ route('roles.index') }}"
                        class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit"
                        class="btn btn-primary">
                        {{ isset($role) ? 'Update Role' : 'Create Role' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('input[name="name"]').on('keyup', function() {
            let slug = $(this)
                .val()
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
            $('#slug').val(slug);
        });
    });
</script>
<script>
$(document).ready(function() {
    $('.expand-icon').click(function() {
        $(this).toggleClass('expanded');
        $(this).closest('.tree-node').find('> .tree-node-children').slideToggle(200);
    });
    /*Parent checkbox select/deselect all children */
    $('.parent-checkbox').change(function() {
        let isChecked = $(this).is(':checked');
        let parentId = $(this).data('parent-id');
        $(`.child-checkbox[data-parent-id="${parentId}"]`).prop('checked', isChecked);
        $(`.nested-checkbox[data-parent-id="${parentId}"]`).prop('checked', isChecked);
    });
    
    /*When any child checkbox changes, update parent checkbox state*/
    $('.child-checkbox, .nested-checkbox').change(function() {
        let parentId = $(this).data('parent-id');
        let parentCheckbox = $(`#menu_${parentId}`);        
        let allChildren = $(`.child-checkbox[data-parent-id="${parentId}"], .nested-checkbox[data-parent-id="${parentId}"]`);
        let checkedChildren = allChildren.filter(':checked');
        if (checkedChildren.length === allChildren.length && allChildren.length > 0) {
            parentCheckbox.prop('checked', true);
            parentCheckbox.prop('indeterminate', false);
        } else if (checkedChildren.length > 0) {
            parentCheckbox.prop('checked', false);
            parentCheckbox.prop('indeterminate', true);
        } else {
            parentCheckbox.prop('checked', false);
            parentCheckbox.prop('indeterminate', false);
        }
    });    
    /*Select All Menus*/
    $('#selectAllMenus').click(function() {
        $('input[type="checkbox"][name="menus[]"]').prop('checked', true);
        $('.parent-checkbox').prop('checked', true).prop('indeterminate', false);
    });
    /* Deselect All Menus */
    $('#deselectAllMenus').click(function() {
        $('input[type="checkbox"][name="menus[]"]').prop('checked', false);
        $('.parent-checkbox').prop('checked', false).prop('indeterminate', false);
    });    
    
});
</script>
@endpush