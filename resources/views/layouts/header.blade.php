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
                    @if(user() && !user()->hasRole('driver'))
                        <a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
                    @endif
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
                        @if (!user()->hasRole('driver') && !user()->hasRole('client'))
                            <a href="https://crmta7.ru/" style="font-size: 18px; font-weight: bold" class="mr-3"
                               target="_blank">Полезная информация</a>
                        @endif

                        @if(user()->hasRole('client') && config('payment-qr-code.payment_qr_code_enable'))
                            <button type="button" class="btn btn-success px-5" data-toggle="modal"
                                    data-target="#paymentQrCodeModal">Внести оплату
                            </button>
                        @endif

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
                                    <span class="d-none d-sm-inline ml-3">{{ ($driver = App\Driver::where('hash_id', user()->login)->first()) ? $driver->fio : '' }}<i
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
