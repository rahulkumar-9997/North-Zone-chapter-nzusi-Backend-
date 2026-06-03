<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Str;

class RoleController extends Controller
{    
    public function index()
    {
        $roles = Role::withCount('users')->latest()->paginate(10);
        return view('backend.pages.roles.index', compact('roles'));
    }

    /**
     * Show form for creating a new role.
     */
    public function create()
    {
        return view('backend.pages.roles.create');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show form for editing role.
     */
    public function edit(Role $role)
    {
        return view('backend.roles.edit', compact('role'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => $request->slug ?? Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of roles that have users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users.');
        }

        // Remove menu associations
        $role->menus()->detach();
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Display role menus management page.
     */
    public function menus(Role $role)
    {
        $menus = Menu::with('children')->whereNull('parent_id')->orderBy('order')->get();
        $roleMenus = $role->menus->pluck('id')->toArray();
        
        return view('backend.roles.menus', compact('role', 'menus', 'roleMenus'));
    }

    /**
     * Update role menus.
     */
    public function updateMenus(Request $request, Role $role)
    {
        $request->validate([
            'menus' => 'array',
            'menus.*' => 'exists:menus,id'
        ]);

        $role->menus()->sync($request->menus ?? []);

        return redirect()->route('roles.menus', $role)
            ->with('success', 'Role menus updated successfully.');
    }
}