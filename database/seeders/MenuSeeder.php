<?php
// database/seeders/MenuSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'ti ti-layout-grid',
                'route' => 'dashboard',
                'url' => null,
                'parent_id' => null,
                'order' => 1,
                'target' => '_self',
                'status' => true,
            ],
            [
                'name' => 'Manage Menu',
                'slug' => 'manage-menu',
                'icon' => 'ti ti-menu',
                'route' => 'menus.index',
                'url' => null,
                'parent_id' => null,
                'order' => 2,
                'target' => '_self',
                'status' => true,
            ],
            [
                'name' => 'Manage User',
                'slug' => 'manage-user',
                'icon' => 'ti ti-users',
                'route' => null,
                'url' => null,
                'parent_id' => null,
                'order' => 3,
                'target' => '_self',
                'status' => true,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'icon' => 'ti ti-user',
                'route' => 'users.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 1,
                'target' => '_self',
                'status' => true,
            ],
            
            // Role Submenu
            [
                'name' => 'Role',
                'slug' => 'role',
                'icon' => 'ti ti-shield',
                'route' => 'roles.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 2,
                'target' => '_self',
                'status' => true,
            ],
            
            // Manage Blog (Parent Menu)
            [
                'name' => 'Manage Blog',
                'slug' => 'manage-blog',
                'icon' => 'ti ti-brand-blogger',
                'route' => null,
                'url' => null,
                'parent_id' => null,
                'order' => 4,
                'target' => '_self',
                'status' => true,
            ],
            
            // Label Submenu
            [
                'name' => 'Label',
                'slug' => 'label',
                'icon' => 'ti ti-tag',
                'route' => 'label.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 1,
                'target' => '_self',
                'status' => true,
            ],
            
            // Category Submenu
            [
                'name' => 'Category',
                'slug' => 'blog-category',
                'icon' => 'ti ti-folder',
                'route' => 'blog-category.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 2,
                'target' => '_self',
                'status' => true,
            ],
            
            // Subcategory Submenu
            [
                'name' => 'Subcategory',
                'slug' => 'blog-subcategory',
                'icon' => 'ti ti-folders',
                'route' => 'blog-subcategory.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 3,
                'target' => '_self',
                'status' => true,
            ],
            
            // Blog Post Submenu
            [
                'name' => 'Blog Post',
                'slug' => 'blog-post',
                'icon' => 'ti ti-news',
                'route' => 'blog-post.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 4,
                'target' => '_self',
                'status' => true,
            ],
            
            // Manage Member (Parent Menu)
            [
                'name' => 'Manage Member',
                'slug' => 'manage-member',
                'icon' => 'ti ti-user',
                'route' => null,
                'url' => null,
                'parent_id' => null,
                'order' => 5,
                'target' => '_self',
                'status' => true,
            ],
            
            // Member Type Submenu
            [
                'name' => 'Member Type',
                'slug' => 'member-type',
                'icon' => 'ti ti-tags',
                'route' => 'member-type.index',
                'url' => null,
                'parent_id' => null, // Will update after
                'order' => 1,
                'target' => '_self',
                'status' => true,
            ],
            
            // Member Submenu
            [
                'name' => 'Member',
                'slug' => 'member',
                'icon' => 'ti ti-user-list',
                'route' => 'manage-member.index',
                'url' => null,
                'parent_id' => null, 
                'order' => 2,
                'target' => '_self',
                'status' => true,
            ],
            
            // Abstract Submission
            [
                'name' => 'Abstract Submission',
                'slug' => 'abstract-submission',
                'icon' => 'ti ti-file-text',
                'route' => 'abstract-submission.index',
                'url' => null,
                'parent_id' => null,
                'order' => 6,
                'target' => '_self',
                'status' => true,
            ],
        ];
        
        // First insert all menus
        foreach ($menus as $menu) {
            Menu::create($menu);
        }
        
        // ========== UPDATE PARENT RELATIONSHIPS ==========
        
        // Get all menus by slug
        $dashboard = Menu::where('slug', 'dashboard')->first();
        $manageMenu = Menu::where('slug', 'manage-menu')->first();
        $manageUser = Menu::where('slug', 'manage-user')->first();
        $user = Menu::where('slug', 'user')->first();
        $role = Menu::where('slug', 'role')->first();
        $manageBlog = Menu::where('slug', 'manage-blog')->first();
        $label = Menu::where('slug', 'label')->first();
        $blogCategory = Menu::where('slug', 'blog-category')->first();
        $blogSubcategory = Menu::where('slug', 'blog-subcategory')->first();
        $blogPost = Menu::where('slug', 'blog-post')->first();
        $manageMember = Menu::where('slug', 'manage-member')->first();
        $memberType = Menu::where('slug', 'member-type')->first();
        $member = Menu::where('slug', 'member')->first();
        $abstractSubmission = Menu::where('slug', 'abstract-submission')->first();
        
        // Update parent relationships
        if ($user && $manageUser) {
            $user->parent_id = $manageUser->id;
            $user->save();
        }
        
        if ($role && $manageUser) {
            $role->parent_id = $manageUser->id;
            $role->save();
        }
        
        if ($label && $manageBlog) {
            $label->parent_id = $manageBlog->id;
            $label->save();
        }
        
        if ($blogCategory && $manageBlog) {
            $blogCategory->parent_id = $manageBlog->id;
            $blogCategory->save();
        }
        
        if ($blogSubcategory && $manageBlog) {
            $blogSubcategory->parent_id = $manageBlog->id;
            $blogSubcategory->save();
        }
        
        if ($blogPost && $manageBlog) {
            $blogPost->parent_id = $manageBlog->id;
            $blogPost->save();
        }
        
        if ($memberType && $manageMember) {
            $memberType->parent_id = $manageMember->id;
            $memberType->save();
        }
        
        if ($member && $manageMember) {
            $member->parent_id = $manageMember->id;
            $member->save();
        }
        
        $this->command->info('Menus created successfully!');
        $this->command->info('Total menus: ' . Menu::count());
    }
}