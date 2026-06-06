<div class="header">
   <div class="main-header">
      <div class="header-left active">
         <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
         </a>
         <a href="{{ route('dashboard') }}" class="logo logo-white">
            <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
         </a>
         <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
         </a>
      </div>
      <!-- /Logo -->
      <a id="mobile_btn" class="mobile_btn" href="#sidebar">
         <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
         </span>
      </a>
      
      <ul class="nav user-menu">
         @if(request()->routeIs('dashboard'))
         <li class="nav-item pos-nav">
            <input type="text" id="daterange" class="form-control" />
         </li>
         @endif         
         <li class="nav-item pos-nav">
            <a href="{{ route('clear-cache') }}" class="btn btn-dark btn-md d-inline-flex align-items-center">
               <i class="ti ti-device-laptop me-1"></i>Clear Cache
            </a>
         </li>         
         @php
            $user = auth()->user();
            $colors = [
               '#0d6efd',
               '#198754',
               '#dc3545',
               '#fd7e14',
               '#6f42c1',
               '#20c997',
               '#6610f2'
            ];
            $bgColor = $user
               ? $colors[$user->id % count($colors)]
               : '#0d6efd';
         @endphp
         <li class="nav-item dropdown has-arrow main-drop profile-nav">
            <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
               <span class="user-info p-0">
                     <span class="user-letter">
                        @if($user && !empty($user->profile_img))
                           <img
                                 src="{{ asset('storage/images/users-profile/' . $user->profile_img) }}"
                                 alt="{{ $user->name }}"
                                 class="img-fluid rounded-circle"
                                 style="width:40px;height:40px;object-fit:cover;"
                           >
                        @else
                           <div
                                 class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:40px;height:40px;background:{{ $bgColor }};"
                           >
                                 {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                           </div>
                        @endif
                     </span>
               </span>
            </a>
            <div class="dropdown-menu menu-drop-user">
               <div class="profileset d-flex align-items-center">
                     <span class="user-img me-2">
                        @if($user && !empty($user->profile_img))
                           <img
                                 src="{{ asset('storage/images/users-profile/' . $user->profile_img) }}"
                                 alt="{{ $user->name }}"
                                 width="40"
                                 height="40"
                                 class="rounded-circle"
                                 style="object-fit:cover;"
                           >
                        @else
                           <div
                                 class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:40px;height:40px;background:{{ $bgColor }};font-size:18px;"
                           >
                                 {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                           </div>
                        @endif
                     </span>
                     <div>
                        <h6 class="fw-medium">{{ $user->name ?? '' }}</h6>
                        <p>{{ $user->user_id ?? '' }}</p>
                     </div>
               </div>
               <a class="dropdown-item" href="{{ route('dashboard') }}">
                     <i class="ti ti-user-circle me-2"></i> My Profile
               </a>
               <hr class="my-2">
               <a class="dropdown-item logout pb-0" href="{{ route('logout') }}">
                     <i class="ti ti-logout me-2"></i> Logout
               </a>
            </div>
         </li>
      </ul>
      <!-- /Header Menu -->

      <!-- Mobile Menu -->
      <div class="dropdown mobile-user-menu">
         <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
         <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#">My Profile</a>
            <a class="dropdown-item" href="#">Settings</a>
            <a class="dropdown-item" href="{{route('logout')}}">Logout</a>
         </div>
      </div>
      <!-- /Mobile Menu -->
   </div>
</div>
<!-- /Header -->