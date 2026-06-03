<?php
namespace App\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
     use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_admin',
        'profile_img',
        'phone_number',
        'date_of_birth',
        'gender',
        'bio',
        'last_login_at',
        'last_login_ip',
        'is_active',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * User ke multiple roles (Many-to-Many)
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
                    ->withTimestamps();
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($roleSlug): bool
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }
    
    /**
     * Check if user has any of given roles
     */
    public function hasAnyRole($roles): bool
    {
        if (is_array($roles)) {
            return $this->roles()->whereIn('slug', $roles)->exists();
        }
        return $this->hasRole($roles);
    }
    
    /**
     * Assign role to user (multiple roles allowed)
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }
        
        if ($role && !$this->roles()->where('role_id', $role->id)->exists()) {
            $this->roles()->attach($role->id);
        }
        
        return $this;
    }
    
    /**
     * Remove role from user
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }
        
        if ($role) {
            $this->roles()->detach($role->id);
        }
        
        return $this;
    }
    
    /**
     * Sync user roles (remove all and assign new)
     */
    public function syncRoles(array $roleIds)
    {
        $this->roles()->sync($roleIds);
        return $this;
    }
    
    /**
     * Get all menu IDs from user's all roles
     */
    public function getAllowedMenuIds()
    {
        $menuIds = collect();
        
        foreach ($this->roles as $role) {
            foreach ($role->menus as $menu) {
                $menuIds->push($menu->id);
            }
        }
        
        return $menuIds->unique();
    }
    
    /**
     * Get hierarchical menus for sidebar (combining menus from all roles)
     */
    public function getSidebarMenus()
    {
        $menuIds = $this->getAllowedMenuIds();
        
        if ($menuIds->isEmpty()) {
            return collect();
        }
        
        // Get parent menus with their children
        return Menu::with(['children' => function($query) use ($menuIds) {
            $query->whereIn('id', $menuIds)
                  ->where('status', true)
                  ->orderBy('order');
        }])
        ->whereIn('id', $menuIds)
        ->whereNull('parent_id')
        ->where('status', true)
        ->orderBy('order')
        ->get();
    }
    
    /**
     * Get user role names as string
     */
    public function getRoleNamesAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }
    
    /**
     * Get user role badges HTML
     */
    public function getRoleBadgesAttribute(): string
    {
        $badges = '';
        foreach ($this->roles as $role) {
            $badges .= '<span class="badge badge-primary mr-1">' . e($role->name) . '</span>';
        }
        return $badges;
    }
}
