<!-- Main Navbar-->
<header class="header no-print">
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
            <!-- Navbar Header-->
            <div class="navbar-header">
                <!-- Navbar Brand --><a href="/" class="navbar-brand d-none d-sm-inline-block">

                <img src="{{ Storage::url(App\Settings::setting('logo')) }}" width="150" alt="">

                </a>
                <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
            </div>
            <!-- Navbar Menu -->
            <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">

                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Войти') }}</a>
                    </li>
                    @if (Route::has('register'))
                        {{--<li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Регистрация') }}</a>
                        </li>--}}
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile') }}"><span class="d-none d-sm-inline">{{ __(Auth::user()->name) }}</span> <i class="fa fa-user"></i></a>
                    </li>
                    <!-- Logout    -->
                    <li class="nav-item"><a href="{{ route('logout') }}" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();" class="nav-link logout"> <span class="d-none d-sm-inline">{{ __('Выйти') }}</span><i class="fa fa-sign-out"></i></a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @endguest
            </ul>
            </div>
        </div>
    </nav>
</header>
