<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display a listing of menus.
     */
    public function index()
    {
        $menus = Menu::query()
            ->with(['children' => function($q) {
                $q->active()->orderBy('order');
            }, 'roles'])
            ->parent()
            ->orderBy('order')
            ->paginate(50);
        
        return view('backend.pages.menu.index', compact('menus'));
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
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', 'unique:menus,slug'],
            'icon'      => ['nullable', 'string', 'max:100'],
            'route'     => ['nullable', 'string', 'max:255'],
            'url'       => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menus,id'],
            'order'     => ['nullable', 'integer', 'min:0'],
            'target'    => ['nullable', 'in:_self,_blank'],
            'status'    => ['nullable', 'boolean'],
        ]);
        DB::beginTransaction();
        try {
            Menu::create([
                'name'      => trim($validated['name']),
                'slug'      => trim($validated['slug']),
                'icon'      => $validated['icon'] ?? null,
                'route'     => $validated['route'] ?? null,
                'url'       => $validated['url'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'order'     => $validated['order'] ?? 0,
                'target'    => $validated['target'] ?? '_self',
                'status'    => $request->boolean('status'),
            ]);
            DB::commit();
            return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu Creation Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return back()->withInput()->with('error', 'Something went wrong while creating the menu.');
        }
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
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string', 'max:255', 'unique:menus,slug,' . $menu->id],
            'icon'      => ['nullable', 'string', 'max:100'],
            'route'     => ['nullable', 'string', 'max:255'],
            'url'       => ['nullable', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:menus,id',
                'not_in:' . $menu->id
            ],
            'order'     => ['nullable', 'integer', 'min:0'],
            'target'    => ['nullable', 'in:_self,_blank'],
            'status'    => ['nullable', 'boolean'],
        ]);
        DB::beginTransaction();
        try {
            $menu->update([
                'name'      => trim($validated['name']),
                'slug'      => trim($validated['slug']),
                'icon'      => $validated['icon'] ?? null,
                'route'     => $validated['route'] ?? null,
                'url'       => $validated['url'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'order'     => $validated['order'] ?? 0,
                'target'    => $validated['target'] ?? '_self',
                'status'    => $request->boolean('status'),
            ]);
            DB::commit();
            return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Menu Update Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return back()->withInput()->with('error', 'Something went wrong while updating the menu.');
        }
    }

    /**
     * Remove the specified menu.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->children()->count() > 0) {
            return back()->with('error', 'Cannot delete menu with child menus. Delete child menus first.');
        }
        $menu->roles()->detach();
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }

    /**
     * Update menu order.
     */
    public function updateOrder(Request $request, Menu $menu)
    {
        $request->validate([
            'direction' => 'required|in:up,down'
        ]);
        DB::beginTransaction();
        try {
            if ($request->direction == 'up') {
                $swapMenu = Menu::where('parent_id', $menu->parent_id)
                    ->where('order', '<', $menu->order)
                    ->orderBy('order', 'desc')
                    ->first();
            } else {
                $swapMenu = Menu::where('parent_id', $menu->parent_id)
                    ->where('order', '>', $menu->order)
                    ->orderBy('order', 'asc')
                    ->first();
            }
            if ($swapMenu) {
                $currentOrder = $menu->order;
                $menu->update([
                    'order' => $swapMenu->order
                ]);
                $swapMenu->update([
                    'order' => $currentOrder
                ]);
            }
            DB::commit();
            $menus = Menu::query()
                ->with([
                    'children' => function ($q) {
                        $q->orderBy('order');
                    },
                    'roles',
                    'parent'
                ])
                ->whereNull('parent_id')
                ->orderBy('order')
                ->paginate(50);
            $html = view(
                'backend.pages.menu.partials.menu-list',
                compact('menus')
            )->render();
            return response()->json([
                'status' => true,
                'message' => 'Menu order updated successfully.',
                'menuContent' => $html
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle menu status.
     */
    public function toggleStatus(Menu $menu)
    {
        try {
            $menu->update([
                'status' => !$menu->status
            ]);
            $menus = Menu::query()
                ->with([
                    'children' => function ($q) {
                        $q->orderBy('order');
                    },
                    'roles',
                    'parent'
                ])
                ->whereNull('parent_id')
                ->orderBy('order')
                ->paginate(50);
            $html = view(
                'backend.pages.menu.partials.menu-list',
                compact('menus')
            )->render();
            return response()->json([
                'status' => true,
                'message' => 'Menu status updated successfully.',
                'menuContent' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleSidebarStatus(Menu $menu)
    {
        try {
            $menu->update([
                'sidebar_status' => !$menu->sidebar_status
            ]);
            $menus = Menu::query()
                ->with([
                    'children' => function ($q) {
                        $q->orderBy('order');
                    },
                    'roles',
                    'parent'
                ])
                ->whereNull('parent_id')
                ->orderBy('order')
                ->paginate(50);
            $html = view(
                'backend.pages.menu.partials.menu-list',
                compact('menus')
            )->render();
            return response()->json([
                'status' => true,
                'message' => 'Menu sidebar status updated successfully.',
                'menuContent' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}