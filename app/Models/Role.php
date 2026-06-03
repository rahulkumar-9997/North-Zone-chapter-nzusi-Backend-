<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];
    
    /**
     * Users who have this role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
    
    /**
     * Menus assigned to this role
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'role_menu', 'role_id', 'menu_id');
    }
    
    /**
     * Assign menu to role
     */
    public function assignMenu($menu)
    {
        if (is_string($menu)) {
            $menu = Menu::where('slug', $menu)->first();
        }
        
        if ($menu && !$this->menus()->where('menu_id', $menu->id)->exists()) {
            $this->menus()->attach($menu->id);
        }
        
        return $this;
    }
    
    /**
     * Remove menu from role
     */
    public function removeMenu($menu)
    {
        if (is_string($menu)) {
            $menu = Menu::where('slug', $menu)->first();
        }
        
        if ($menu) {
            $this->menus()->detach($menu->id);
        }
        
        return $this;
    }
    
    /**
     * Sync menus for role
     */
    public function syncMenus(array $menuIds)
    {
        $this->menus()->sync($menuIds);
        return $this;
    }
}