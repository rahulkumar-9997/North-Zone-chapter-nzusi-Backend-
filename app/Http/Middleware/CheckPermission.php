<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Menu;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }        
        $user = auth()->user();
        $currentRoute = $request->route()->getName();
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        if ($user->is_admin==1) {
            return $next($request);
        }
        $allowedMenuIds = collect();
        foreach ($user->roles as $role) {
            foreach ($role->menus as $menu) {
                $allowedMenuIds->push($menu->id);
            }
        }
        $currentMenu = Menu::where('route', $currentRoute)->first();
        if (!$currentMenu) {
            return $next($request);
        }
        if (!$allowedMenuIds->contains($currentMenu->id)) {
            abort(403, 'You do not have permission to access this page.');
        }        
        return $next($request);
    }
}