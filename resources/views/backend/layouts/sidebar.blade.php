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

                  {{-- Manage Pages --}}
                  <li class="submenu {{ request()->routeIs('pages.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-brand-apple-arcade fs-16 me-2"></i>
                        <span>Manage Pages</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('pages.*') ? 'display:block;' : '' }}">
                        <li class="{{ request()->routeIs('pages.index') ? 'active' : '' }}">
                           <a href="{{ route('pages.index') }}">All Pages</a>
                        </li>
                        <li class="{{ request()->routeIs('pages.create') ? 'active' : '' }}">
                           <a href="{{ route('pages.create') }}">Create Page</a>
                        </li>
                     </ul>
                  </li>

                  {{-- Manage Menus --}}
                  <li class="submenu {{ request()->routeIs('menus.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-layout-grid-add fs-16 me-2"></i>
                        <span>Manage Menus</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('menus.*') ? 'display:block;' : '' }}">
                        <li class="{{ request()->routeIs('menus.index') ? 'active' : '' }}">
                           <a href="{{ route('menus.index') }}">All Menus</a>
                        </li>
                        <li class="{{ request()->routeIs('menus.create') ? 'active' : '' }}">
                           <a href="{{ route('menus.create') }}">Create Menu</a>
                        </li>
                     </ul>
                  </li>

                  {{-- Manage Blog --}}
                  <li class="submenu {{ request()->routeIs('blog-category.*') || request()->routeIs('blog-post.*') ? 'open active' : '' }}">
                     <a href="javascript:void(0);">
                        <i class="ti ti-brand-blogger fs-16 me-2"></i>
                        <span>Manage Blog</span>
                        <span class="menu-arrow"></span>
                     </a>
                     <ul style="{{ request()->routeIs('blog-category.*') || request()->routeIs('blog-post.*') ? 'display:block;' : '' }}">
                        <li class="{{ request()->routeIs('blog-category.*') ? 'active' : '' }}">
                           <a href="{{ route('blog-category.index') }}">Category</a>
                        </li>
                        <li class="{{ request()->routeIs('blog-post.*') ? 'active' : '' }}">
                           <a href="{{ route('blog-post.index') }}">Blog Post</a>
                        </li>
                     </ul>
                  </li>

               </ul>
            </li>
         </ul>
      </div>
   </div>
</div>
<!-- /Sidebar -->