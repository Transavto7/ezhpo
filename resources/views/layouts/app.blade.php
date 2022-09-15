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

    {{-- Disabled Cache --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">

    <!-- Styles -->
    <style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css" integrity="sha512-yVvxUQV0QESBt1SyZbNJMAwyKvFTLMyXSyBHDO4BG5t7k/Lw34tyqlSDlKIrIENIzCl+RVUNjmCPG+V/GMesRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/css/suggestions.min.css" rel="stylesheet" />
    <link href="{{ mix('css/app.css') }}?v={{ time() }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">

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
        window.addEventListener("load", function(event) {
            const preloader = document.querySelector('#page-preloader');
            preloader.classList.add('hide');
        });
    </script>

    @yield('custom-styles')
</head>
<body>
{{--    <div id="page-preloader" class="preloader">--}}
{{--        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>--}}
{{--        <span class="text-white">Загрузка</span>--}}
{{--    </div>--}}

    <div id="app" class="page @yield('class-page')">
        @include('layouts.header')

        @auth
            @if(auth()->user()->role === 4)
                <notify></notify>
            @endif
        @endauth

        <main class="page-content d-flex align-items-stretch @if (user() && (user()->hasRole('driver') || user()->hasRole('client'))) blue @endif">

            @hasSection ('sidebar')
                @auth
                    @include('layouts.sidebar')
                @endauth
            @endif

            @guest
                @yield('content')
            @endguest

            @auth
                <div
                    class="content-inner @hasSection ('sidebar') @else w-100 @endif"
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
                    <section class="no-padding-bottom">
                        <div class="page-container container-fluid">
                            @yield('content')
                        </div>
                    </section>

                        @if (user() && !user()->hasRole('driver') && !user()->hasRole('client'))
                            <footer class="footer no-print" style="background-color: rgb(255, 255, 255)">
                                <div class="d-flex justify-content-between footer-inner">
                                    <div class="text-center" style="vertical-align: middle">
        {{--                                Сделано в--}}
        {{--                                <a href="https://nozdratenko.ru" target="_blank" class="cp-logo">nozdr<span>a</span>tenko</a>--}}
                                    </div>
                                    <div class="d-flex align-items-center">
                                            <a href="https://crmta7.ru/" target="_blank">Полезная информация</a>
                                    </div>
                                </div>
                            </footer>
                        @endif
                </div>
            @endauth
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.min.js" integrity="sha512-Bkf3qaV86NxX+7MyZnLPWNt0ZI7/OloMlRo8z8KPIEUXssbVwB1E0bWVeCvYHjnSPwh4uuqDryUnRdcUw6FoTg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/TableExport/5.2.0/js/tableexport.min.js" integrity="sha512-XmZS54be9JGMZjf+zk61JZaLZyjTRgs41JLSmx5QlIP5F+sSGIyzD2eJyxD4K6kGGr7AsVhaitzZ2WTfzpsQzg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@20.3.0/dist/js/jquery.suggestions.min.js"></script>

    <script src="{{ asset('js/app.js') }}?v={{ time() }}" type="text/javascript"></script>

    @yield('custom-scripts')
</body>
</html>
