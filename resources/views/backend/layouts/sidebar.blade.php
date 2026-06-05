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
                        @php
                            $menuUrl = '#';

                            if (!empty($menu->route) && Route::has($menu->route)) {
                                $routeObj = Route::getRoutes()->getByName($menu->route);

                                if ($routeObj && count($routeObj->parameterNames()) === 0) {
                                    $menuUrl = route($menu->route);
                                }
                            } elseif (!empty($menu->url)) {
                                $menuUrl = $menu->url;
                            }
                        @endphp
                      @if($menu->children->isEmpty())
                          
                          <li class="{{ request()->routeIs($menu->route) ? 'active' : '' }}">
                                <a href="{{ $menuUrl }}" target="{{ $menu->target ?? '_self' }}">
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
                                        @php
                                            $childUrl = '#';

                                            if (!empty($child->route) && Route::has($child->route)) {
                                                $routeObj = Route::getRoutes()->getByName($child->route);

                                                if ($routeObj && count($routeObj->parameterNames()) === 0) {
                                                    $childUrl = route($child->route);
                                                }
                                            } elseif (!empty($child->url)) {
                                                $childUrl = $child->url;
                                            }
                                        @endphp
                                      <li class="{{ !empty($child->route) && request()->routeIs($child->route) ? 'active' : '' }}">
                                        <a href="{{ $childUrl }}" target="{{ $child->target ?? '_self' }}">
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