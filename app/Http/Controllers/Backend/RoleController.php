<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Menu;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        /** @var User $user */
        $user = Auth::user();
        $menusQuery = Menu::with('children')
            ->whereNull('parent_id')
            ->where('status', true);
        if (!isset($user->is_admin) || $user->is_admin != 1) {
            $hiddenMenus = ['manage-menu', 'menus', 'roles', 'users'];
            $menusQuery->whereNotIn('slug', $hiddenMenus);
        }        
        $menus = $menusQuery->orderBy('order')->get();
        
        return view('backend.pages.roles.create', compact('menus'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:roles,name',
            'slug'      => 'nullable|string|max:255|unique:roles,slug',
            'is_active' => 'nullable|boolean',
            'menus' => 'array',
            'menus.*' => 'exists:menus,id'
        ]);
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'slug' => !empty($validated['slug'])
                    ? Str::slug($validated['slug'])
                    : Str::slug($validated['name']),
                'is_active' => $request->has('is_active')
            ]);

            if ($request->has('menus')) {
                $role->menus()->sync($request->menus);
            }
            DB::commit();
            return redirect()->route('roles.index')->with('success','Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->withInput()->with('error','Failed to create role.');
        }
    }

    /**
     * Show form for editing role.
     */
    public function edit(Role $role)
    {
        $user = Auth::user();
        $menusQuery = Menu::with('children')
            ->whereNull('parent_id')
            ->where('status', true);
        
        if (!isset($user->is_admin) || $user->is_admin != 1) {
            $hiddenMenus = ['manage-menu', 'menus', 'roles', 'users'];
            $menusQuery->whereNotIn('slug', $hiddenMenus);
        }        
        $menus = $menusQuery->orderBy('order')->get();
        $roleMenus = $role->menus->pluck('id')->toArray();
        
        return view('backend.pages.roles.create', compact('role', 'menus', 'roleMenus'));
    }
    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug'      => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'is_active' => 'nullable|boolean',
            'menus' => 'array',
            'menus.*' => 'exists:menus,id'
        ]);
        DB::beginTransaction();
        try {
            $role->update([
                'name' => $validated['name'],
                'slug' => !empty($validated['slug'])
                    ? Str::slug($validated['slug'])
                    : Str::slug($validated['name']),
                'is_active' => $request->has('is_active')
            ]);
            $role->menus()->sync($request->menus ?? []);
            DB::commit();
            return redirect()->route('roles.index')->with('success','Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->withInput()->with('error','Failed to update role.');
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        DB::beginTransaction();
        try {
            if ($role->users()->count()) {
                return back()->with(
                    'error',
                    'Cannot delete role assigned to users.'
                );
            }
            $role->menus()->detach();
            $role->delete();
            DB::commit();
            return redirect()->route('roles.index')->with('success','Role deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with(
                'error',
                'Failed to delete role.'
            );
        }
    }    
}