<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['name', 'slug', 'icon', 'route', 'url', 'parent_id', 'order', 'status', 'target', 'sidebar_status'];
    
    protected $casts = [
        'status' => 'boolean',
        'sidebar_status' => 'boolean',
    ];
    
    /**
     * Parent menu
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }
    
    /**
     * Child menus
     */
    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Roles that have this menu
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_menu', 'menu_id', 'role_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Get menus for a specific user based on their roles
     */
    public static function getUserMenus($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return collect();
        }
        $user = User::with('roles.menus')->find($userId);
        if (!$user) {
            return collect();
        }
        if ($user->is_admin == 1) {
            return self::with([
                'children' => function ($query) {
                    $query->where('status', true)
                        ->where('sidebar_status', true)
                        ->orderBy('order');
                }
            ])
            ->whereNull('parent_id')
            ->where('status', true)
            ->where('sidebar_status', true)
            ->orderBy('order')
            ->get();
        }
        $menuIds = $user->roles
            ->flatMap(fn ($role) => $role->menus->pluck('id'))
            ->unique();

        if ($menuIds->isEmpty()) {
            return collect();
        }
        return self::with([
            'children' => function ($query) use ($menuIds) {
                $query->whereIn('id', $menuIds)
                    ->where('status', true)
                    ->where('sidebar_status', true)
                    ->orderBy('order');
            }
        ])
        ->whereIn('id', $menuIds)
        ->whereNull('parent_id')
        ->where('status', true)
        ->where('sidebar_status', true)
        ->orderBy('order')
        ->get();
    }
}