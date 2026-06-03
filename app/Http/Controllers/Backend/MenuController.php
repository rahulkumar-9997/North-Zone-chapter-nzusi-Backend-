<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index()
    {
        $menus = Menu::with(['children', 'parent'])
            ->orderBy('order')
            ->get();
        
        $parentMenus = Menu::whereNull('parent_id')->orderBy('order')->get();
        
        return view('backend.pages.menu.index', compact('menus', 'parentMenus'));
    }

    /**
     * Show form for creating a new menu.
     */
    public function create()
    {
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();
        return view('backend.pages.menu.create', compact('parents'));
    }

    /**
     * Store a newly created menu.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug',
            'icon' => 'nullable|string|max:100',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'target' => 'nullable|in:_self,_blank',
            'status' => 'nullable|boolean'
        ]);

        Menu::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'icon' => $request->icon,
            'route' => $request->route,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'target' => $request->target ?? '_self',
            'status' => $request->status ?? true
        ]);

        return redirect()->route('menus.index')
            ->with('success', 'Menu created successfully.');
    }

    /**
     * Show form for editing menu.
     */
    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();
        
        return view('backend.pages.menu.edit', compact('menu', 'parents'));
    }

    /**
     * Update the specified menu.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:menus,slug,' . $menu->id,
            'icon' => 'nullable|string|max:100',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'target' => 'nullable|in:_self,_blank',
            'status' => 'nullable|boolean'
        ]);

        $menu->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'icon' => $request->icon,
            'route' => $request->route,
            'url' => $request->url,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'target' => $request->target ?? '_self',
            'status' => $request->status ?? true
        ]);

        return redirect()->route('menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        // Check if menu has children
        if ($menu->children()->count() > 0) {
            return back()->with('error', 'Cannot delete menu with child menus. Delete child menus first.');
        }

        // Remove role associations
        $menu->roles()->detach();
        $menu->delete();

        return redirect()->route('menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    /**
     * Update menu order.
     */
    public function updateOrder(Request $request, Menu $menu)
    {
        $request->validate([
            'order' => 'required|integer'
        ]);

        $menu->update(['order' => $request->order]);

        return response()->json(['success' => true]);
    }

    /**
     * Toggle menu status.
     */
    public function toggleStatus($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->status = !$menu->status;
        $menu->save();

        return response()->json([
            'success' => true,
            'status' => $menu->status
        ]);
    }
}