<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="North Zone chapter of Urological Society of India (NZUSI).">    
    <meta name="author" content="NZUSI">
    <link rel="icon" href="{{asset('backend/assets/images/fav.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('backend/assets/images/fav.png')}}" type="image/x-icon">
    <title>NZUSI - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/font-awesome.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/icofont.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/themify.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/responsive.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="authentication-background">
    <div class="container p-0">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100 m-0">
            <div class="col-xxl-5 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                <div class="login-card login-dark my-4">
                    <div>
                        <div>
                            <a class="logo" href="{{route('login')}}">
                                <img class="img-fluid for-dark" src="{{asset('backend/assets/images/logo/nzusi.png')}}" alt="looginpage">
                                <img class="img-fluid for-light" src="{{asset('backend/assets/images/logo/nzusi.png')}}" alt="looginpage">
                            </a>
                        </div>
                        <div class="login-main">
                            <form class="theme-form authentication-form" action="{{route('login')}}" method="post" id="loginForm">
                                @csrf
                                @if(session('success'))
                                    <div class="alert txt-success border-success outline-2x alert-dismissible fade show alert-icons" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert txt-danger border-danger outline-2x alert-dismissible fade show alert-icons" role="alert">
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                
                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <p class="mb-1"><strong class="text-white">Opps Something went wrong</strong></p>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <h4>Sign in to account </h4>
                                <p>Enter your email OR Username & password to login</p>
                                <div class="form-group">
                                    <label class="col-form-label">Email Address Or Username</label>
                                    <input class="form-control" type="text" name="email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Password </label>
                                    <div class="form-input position-relative">
                                        <input class="form-control" type="password" name="password">
                                        <div class="show-hide"> <span class="show"></span></div>
                                    </div>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-0">
                                    <div class="checkbox p-0">
                                        <input id="checkbox1" type="checkbox">
                                        <label class="text-muted" for="checkbox1">Remember password</label>
                                    </div>
                                    <a class="link" href="{{route('forget.password')}}">Forgot password?</a>
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
    <script src="{{asset('backend/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('backend/assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('backend/assets/js/icons/feather-icon/feather.min.js')}}"></script>
    <script src="{{asset('backend/assets/js/icons/feather-icon/feather-icon.js')}}"></script>
    <script src="{{asset('backend/assets/js/script.js')}}"></script>
</body>

</html>