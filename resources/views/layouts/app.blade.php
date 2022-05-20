<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Miguel Alejandro González Antúnez">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Agence') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">

    <!-- Our project just needs Font Awesome Solid + Brands -->
    <link href="{{ asset('css/fontawesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">
    <link href="{{ asset('css/brands.min.css') }}" rel="stylesheet" type="text/css" media="screen">
    <link href="{{ asset('css/solid.min.css') }}" rel="stylesheet" type="text/css" media="screen">
    
    @yield('extra-css')

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @yield('extra-js')    
</head>

<body>    
    <nav class="navbar navbar-expand-md navbar-light bg-light shadow">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('img/logo.gif') }}" alt="{{ config('app.name', 'Agence') }}" width="130" height="40" class="d-inline-block align-top">                
            </a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a id="nbDpdwnComercial" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-chart-column"></i> {{ __('Comercial') }}
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="nbDpdwnComercial">                            
                            <li><a class="dropdown-item" href="{{ route('comercial.index') }}"><i class="fas fa-chart-line text-muted"></i> {{ __('Performance Comercial') }}</a></li>                            
                        </ul>
                    </li>                    
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-alt"></i> {{ __('Unknow') }}
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }} <i class="fas fa-sign-out-alt text-muted"></i></a></li>
                            <form id="logout-form" action="" method="POST" class="d-none">
                                @csrf
                            </form>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>    
    
    <div class="py-3 px-3">
        @yield('content')
    </div>

</body>

</html>