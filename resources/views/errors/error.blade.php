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

    <title>{{ config('app.name', 'TransAvto-7') }}</title>

    <link href="{{ mix('css/app.css') }}?v={{ time() }}" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">
</head>
<body>

<div id="app" class="page page--error">
    <header class="header no-print">
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                    <!-- Navbar Header-->
                    <div class="navbar-header">
                        <!-- Navbar Brand -->
                        <a href="/" class="navbar-brand d-none d-sm-inline-block">
                            {{ config('app.name', 'TransAvto-7') }}
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="page-content d-flex align-items-stretch">
        <div class="container text-center">
            <div class="row">
                <div class="col-12">
                    <img src="{{ asset('images/error.png') }}" width="80%" alt="" />
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h1 class="text-muted">@yield('message')</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h1>
                        <a href="{{url()->previous()}}">
                            Назад
                        </a>
                        <span class="text-muted">|</span>
                        <a href="/">
                            На главную
                        </a>
                    </h1>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
