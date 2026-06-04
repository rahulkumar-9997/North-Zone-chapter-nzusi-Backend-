@php
   $menus = App\Models\Menu::getUserMenus(auth()->id());
@endphp
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
   <div class="user-info text-center p-1 border-bottom">
      <strong>{{ auth()->user()->name }}</strong>
      <div class="mt-1">
         {!! auth()->user()->getRoleBadgesAttribute() !!}
      </div>
   </div>
   <div class="sidebar-inner slimscroll">
      <div id="sidebar-menu" class="sidebar-menu">
         <ul>
            <li class="submenu-open">
               <ul>
                  @forelse($menus as $menu)
                      @if($menu->children->isEmpty())
                          {{-- Single menu item --}}
                          <li class="{{ request()->routeIs($menu->route) ? 'active' : '' }}">
                              <a href="{{ $menu->route ? route($menu->route) : ($menu->url ?? '#') }}" target="{{ $menu->target }}">
                                  <i class="{{ $menu->icon }} fs-16 me-2"></i>
                                  <span>{{ $menu->name }}</span>
                              </a>
                          </li>
                      @else
                          {{-- Parent menu with children --}}
                          @php
                              $isActive = false;
                              foreach($menu->children as $child) {
                                  if(request()->routeIs($child->route)) {
                                      $isActive = true;
                                      break;
                                  }
                              }
                          @endphp
                          
                          <li class="submenu {{ $isActive ? 'open active' : '' }}">
                              <a href="javascript:void(0);">
                                  <i class="{{ $menu->icon }} fs-16 me-2"></i>
                                  <span>{{ $menu->name }}</span>
                                  <span class="menu-arrow"></span>
                              </a>
                              <ul style="{{ $isActive ? 'display:block;' : '' }}">
                                  @foreach($menu->children as $child)
                                      <li class="{{ request()->routeIs($child->route) ? 'active' : '' }}">
                                          <a href="{{ $child->route ? route($child->route) : ($child->url ?? '#') }}" target="{{ $child->target }}">
                                              <!-- <i class="{{ $child->icon ?? 'ti ti-circle' }} fs-14 me-2"></i> -->
                                              {{ $child->name }}
                                          </a>
                                      </li>
                                  @endforeach
                              </ul>
                          </li>
                      @endif
                  @empty
                      <li class="text-center text-muted p-3">
                          <i class="ti ti-alert-circle"></i><br>
                          No menus assigned to your roles
                      </li>
                  @endforelse
               </ul>
            </li>
         </ul>
      </div>
   </div>
</div>
<!-- /Sidebar -->