<!-- Sidebar -->
<div class="sidebar" id="sidebar">
   <!-- Logo -->
   <div class="sidebar-logo active">
      <a href="{{ route('dashboard') }}" class="logo logo-normal">
         <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
      </a>
      <a href="{{ route('dashboard') }}" class="logo logo-white">
         <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
      </a>
      <a href="{{ route('dashboard')}}" class="logo-small">
         <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
      </a>
      <a id="toggle_btn" href="javascript:void(0);">
         <i data-feather="chevrons-left" class="feather-16"></i>
      </a>
   </div>
   <div class="sidebar-inner slimscroll">
      <div id="sidebar-menu" class="sidebar-menu">
         <ul>
            <li class="submenu-open">
               <ul>
                  <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                     <a href="{{ route('dashboard') }}">
                        <i class="ti ti-layout-grid fs-16 me-2"></i>
                        <span>Dashboard</span>
                     </a>
                  </li> 
                  <li class="{{ request()->routeIs('menus.*') ? 'active' : '' }}">
                     <a href="{{ route('menus.index') }}">
                        <i class="ti ti-layout-grid fs-16 me-2"></i>
                        <span>Manage Menu</span>
                     </a>
                  </li> 
                  
                  <li class="submenu {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-brand-blogger fs-16 me-2"></i>
                        <span>Manage User</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'display:block;' : '' }}">                        
                        <li class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                           <a href="{{ route('users.index') }}">User</a>
                        </li>
                        <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                           <a href="{{ route('roles.index') }}">Role</a>
                        </li>
                        
                     </ul>
                  </li>

                  <li class="submenu {{ request()->routeIs('blog-category.*') || request()->routeIs('blog-post.*') || request()->routeIs('blog-subcategory.*') || request()->routeIs('label.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-brand-blogger fs-16 me-2"></i>
                        <span>Manage Blog</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('blog-category.*') || request()->routeIs('blog-post.*') || request()->routeIs('blog-subcategory.*') || request()->routeIs('label.*') ? 'display:block;' : '' }}">                        
                        <li class="{{ request()->routeIs('label.*') ? 'active' : '' }}">
                           <a href="{{ route('label.index') }}">Label</a>
                        </li>
                        <li class="{{ request()->routeIs('blog-category.*') ? 'active' : '' }}">
                           <a href="{{ route('blog-category.index') }}">Category</a>
                        </li>
                        <li class="{{ request()->routeIs('blog-subcategory.*') ? 'active' : '' }}">
                           <a href="{{ route('blog-subcategory.index') }}">Subcategory</a>
                        </li>
                        <li class="{{ request()->routeIs('blog-post.*') ? 'active' : '' }}">
                           <a href="{{ route('blog-post.index') }}">Blog Post</a>
                        </li>
                     </ul>
                  </li>
                  <li class="submenu {{ request()->routeIs('manage-member.*') || request()->routeIs('member-type.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-user fs-16 me-2"></i>
                        <span>Manage Member</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('manage-member.*') || request()->routeIs('member-type.*') ? 'display:block;' : '' }}">
                        <li class="{{ request()->routeIs('member-type.*') ? 'active' : '' }}">
                           <a href="{{ route('member-type.index') }}">Member Type</a>
                        </li>
                        <li class="{{ request()->routeIs('manage-member.*') ? 'active' : '' }}">
                           <a href="{{ route('manage-member.index') }}">Member</a>
                        </li>
                     </ul>
                  </li>
                  <li class="{{ request()->routeIs('abstract-submission.index') ? 'active' : '' }}">
                     <a href="{{ route('abstract-submission.index') }}">
                        <i class="ti ti-file-text fs-16 me-2"></i>
                        <span>Abstract Submission</span>
                     </a>
                  </li>
               </ul>
            </li>
         </ul>
      </div>
   </div>
</div>
<!-- /Sidebar -->