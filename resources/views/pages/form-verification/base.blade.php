<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="all,follow">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}?v={{ time() }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">

    <style>
        body {
            scroll-behavior: smooth;
        }

        header {
            z-index: 1000;
        }
    </style>

    @stack('custom_styles')

    @include('layouts.analytics.yandex-metric')

    @auth
        <script type="text/javascript">
            window.API_TOKEN = '{{ Auth::user()->api_token }}';
            window.userRole = function () {
                return {{ auth()->user()->role }}
            };
        </script>
    @endauth

    @guest
        <script type="text/javascript">
            window.API_TOKEN = '';
            window.userRole = function () {
                return 0;
            };
        </script>
    @endguest

    <script type="text/javascript">
        window.addEventListener("load", function (event) {
            const preloader = document.querySelector('#page-preloader');
            preloader.classList.add('hide');

            const mobileDownload = document.querySelector('.mobile-download');
            if (isAndroid() && mobileDownload) {
                mobileDownload.style.display = 'flex';
            }
        });

        function isAndroid() {
            const ua = navigator.userAgent.toLowerCase();
            return ua.indexOf("android") > -1;
        }
    </script>
    @yield('custom-styles')
</head>
<body>
<div id="page-preloader" class="preloader">
    <div class="lds-ring">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
    <span class="text-white">Загрузка</span>
</div>

<div id="app">
    <!-- Main Navbar-->
    <header class="header no-print position-fixed w-100">
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                    <!-- Navbar Header-->
                    <div class="navbar-header">
                        <!-- Navbar Brand --><a href="/" class="navbar-brand d-none d-sm-inline-block">
                            <img src="{{ Storage::url(App\Settings::setting('logo')) }}" width="150" alt="">
                        </a>
                    </div>
                    <!-- Navbar Menu -->
                    <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Войти') }}</a>
                            </li>
                        @else
                            @php
                                if(user()->photo) {
                                    $user_avatar = asset("storage/". user()->photo);
                                }else{
                                    $user_avatar = asset("img/default_profile.jpg");
                                }
                            @endphp

                            <li class="nav-item">
                                <a class="nav-link flex-center" href="{{ route('profile.index') }}">
                            <span class="client">
                                <span class="client-avatar">
                                    <img src="{{ $user_avatar }}" width="50" alt="avatar">
                                </span>
                            </span>
                                    @if(user()->hasRole('driver'))
                                        <span class="d-none d-sm-inline ml-3">{{ ($driver = \App\Driver::where('hash_id', user()->login)->first()) ? $driver->fio : '' }}<i
                                                class="fa fa-user ml-1"></i></span>
                                        <span class="d-none d-sm-inline ml-3">{{ user()->login }}</span>
                                    @else
                                        <span class="d-none d-sm-inline ml-3">{{ user()->name }}<i
                                                class="fa fa-user ml-1"></i></span>
                                    @endif
                                </a>
                            </li>
                            <!-- Logout    -->
                            <li class="nav-item"><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();" class="nav-link logout"> <span
                                        class="d-none d-sm-inline">{{ __('Выйти') }}</span><i
                                        class="fa fa-sign-out"></i></a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ mix('/js/manifest.js') }}"></script>
<script src="{{ mix('/js/vendor.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>

@stack('custom_scripts')

</body>
</html>
