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
    <style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" integrity="sha512-yVvxUQV0QESBt1SyZbNJMAwyKvFTLMyXSyBHDO4BG5t7k/Lw34tyqlSDlKIrIENIzCl+RVUNjmCPG+V/GMesRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="{{ mix('css/app.css') }}?v={{ time() }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">

    <script>
        window.PAGE_SETUP = {
            baseUrl: '{{ config('app.url') }}'
        }
        window.DOC_FIELDS = @json(config('docs.fields'));
    </script>

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

<div id="app" class="page @yield('class-page')">
    @include('layouts.header')

    <main
        class="page-content d-flex align-items-stretch @if (user() && (user()->hasRole('driver') || user()->hasRole('client'))) blue @endif">

        @if (user() && !user()->hasRole('driver'))
            @include('layouts.sidebar')
        @endif

        @guest
            @yield('content')
        @endguest

        @auth
            <div
                class="content-inner @if (user() && user()->hasRole('driver')) w-100 @endif"
            >
                @hasSection ('sidebar')
                    <!-- Хэдер-->
                    <header class="page-header">
                        <div class="container-fluid">
                            <h2 class="no-margin-bottom">@yield('title')</h2>
                        </div>
                    </header>
                @endif

                <!-- Контент страницы -->
                <section class="pt-3">
                    <div class="page-container container-fluid">
                        @yield('content')
                    </div>
                </section>
            </div>
        @endauth
    </main>
</div>

@if(user() && !user()->accepted_agreement)
    <div class="modal_agreement">
        <div class="modal_agreement__inner">
            <div class="modal_agreement__header">
                Пользовательское соглашение
            </div>

            <div class="modal_agreement__content">
                Перед началом использования вы должны <br>
                прочитать и принять наше<br>
                <a href="/agreement" target="_blank">пользовательское соглашение</a>
            </div>

            <div class="modal_agreement__buttons">
                <button class="btn btn-success" onclick="event.preventDefault();
                        document.getElementById('agreement-form').submit();">Принимаю
                </button>
                <button class="btn btn-danger" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">Выйти
                </button>
            </div>
        </div>
    </div>

    <form id="agreement-form" action="/agreement" method="POST" style="display: none;">
        @csrf
    </form>
@endif

@stack('setup-scripts')

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

<script src="{{ mix('/js/manifest.js') }}"></script>
<script src="{{ mix('/js/vendor.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>

@yield('custom-scripts')
</body>
</html>
