<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="North Zone chapter of Urological Society of India (NZUSI) Forgot Password page.">    
    <meta name="author" content="NZUSI">
    <link rel="icon" href="{{asset('backend/assets/images/fav.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset('backend/assets/images/fav.png')}}" type="image/x-icon">
    <title>NZUSI - Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/font-awesome.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/icofont.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/themify.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/vendors/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('backend/assets/css/responsive.css')}}">
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
                            <form class="theme-form" action="{{ route('reset.password.post') }}" method="post">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">                          
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                                <h4>Reset Password</h4>
                                <div class="form-group">
                                    <label class="col-form-label">Enter Registered Email'id</label>
                                    <input class="form-control" type="email" name="email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Enter New Password</label>
                                    <input class="form-control" type="password" name="password">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">Confirm New Password</label>
                                    <input class="form-control" type="password" name="password_confirmation">
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group mb-0">                                    
                                    <div class="text-end mt-3">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Submit</button>
                                    </div>
                                </div>
                                <div class="signinform text-center mt-2">
                                    <h5>Or Return to<a href="{{route('login')}}" class="hover-a"> login </a></h5>
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