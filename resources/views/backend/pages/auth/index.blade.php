<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="NZUSI">
    <meta name="robots" content="index, follow">
    <title>North Zone chapter of Urological Society of India :: Login</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('backend/assets/images/fav.png')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('backend/assets/images/fav.png')}}">
    <link rel="stylesheet" href="{{asset('backend/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/assets/plugins/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/assets/plugins/tabler-icons/tabler-icons.css')}}">
    <link rel="stylesheet" href="{{asset('backend/assets/css/style.css')}}">
</head>

<body class="account-page authentication-background">
    <div class="main-wrapper">
        <div class="account-content">
            <div class="login-wrapper login-new">
                <div class="row justify-content-center align-items-center authentication authentication-basic">
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-5 col-sm-8 col-12">
                        <div class="login-content user-login">
                            <div class="card custom-card my-4">
                                <div class="card-body p-4">
                                    <div class="login-logo">
                                        <img src="{{asset('backend/assets/images/logo.png')}}" alt="img">
                                        <a href="{{route('login')}}" class="login-logo logo-white">
                                            <img src="{{asset('backend/assets/images/logo.png')}}" alt="Img">
                                        </a>
                                    </div>
                                    <form action="{{route('login')}}" method="post">
                                        @csrf                                        
                                        <div class="login-userheading">
                                            <h3>Sign In</h3>
                                        </div>
                                        @if($errors->any())
                                        <div class="alert alert-danger">
                                            <p class="mb-0"><strong>Opps Something went wrong</strong></p>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        @if(session()->has('error'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('error') }}
                                        </div>
                                        @endif
                                        @if(session()->has('success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('success') }}
                                        </div>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label">Enter your Registered Email OR User Name <span class="text-danger"> *</span></label>
                                            <div class="input-group">
                                                <input type="text" value="" class="form-control border-end-0" name="email">
                                                <span class="input-group-text border-start-0">
                                                    <i class="ti ti-mail"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password <span class="text-danger"> *</span></label>
                                            <div class="pass-group">
                                                <input type="password" class="pass-input form-control" name="password">
                                                <span class="ti toggle-password ti-eye-off text-gray-9"></span>
                                            </div>
                                        </div>
                                        <div class="form-login authentication-check">
                                            <div class="row">
                                                <div class="col-12 d-flex align-items-center justify-content-between">
                                                    <div class="custom-control custom-checkbox">
                                                        <label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
                                                            <input type="checkbox" class="form-control">
                                                            <span class="checkmarks"></span>Remember me
                                                        </label>
                                                    </div>
                                                    <div class="text-end">
                                                        <a class="text-orange fs-16 fw-medium" href="{{route('forget.password')}}">Forgot Password?</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-login">
                                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="my-4 d-flex justify-content-center align-items-center copyright-text">
                            <p class="text-white">Copyright &copy; {{ date('Y') }} North Zone chapter of Urological Society of India. (NZUSI) All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Main Wrapper -->
    <script src="{{asset('backend/assets/js/jquery-3.7.1.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('backend/assets/js/feather.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('backend/assets/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('backend/assets/js/script.js')}}" type="text/javascript"></script>
</body>

</html>