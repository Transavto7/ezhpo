@extends('layouts.app')

@section('title', 'Авторизация')
@section('class-page', 'login-page')

@section('content')

    <div class="container d-flex align-items-center">
        <div class="form-holder">
            <div class="row">
                <!-- Form Panel    -->
                <div class="col-lg-12 bg-white">
                    <div class="form d-flex align-items-center">
                        <div class="content">
                            <form class="m-center" method="POST" action="{{ route('login') }}">
                                <h2>{{ __('Пожалуйста, авторизуйтесь') }}</h2>
                                <hr>
                                @csrf

                                <div class="form-group">
                                    <p>{{ __('Логин') }}</p>
                                    <input id="login" type="text" placeholder="Login..." class="input-material @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="login" autofocus>
                                </div>

                                <div class="form-group">
                                    <p>{{ __('Пароль') }}</p>

                                    <div class="field field--password">
                                        <i class="fa fa-eye-slash"></i>
                                        <input data-toggle="password" id="password" type="password" placeholder="Пароль..." class="input-material @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                    </div>
                                </div>

                                @if($errors->getMessages())
                                    @foreach($errors->getMessages() as $err)
                                        @foreach($err as $errItem)
                                            <span class="alert alert-danger">{{ $errItem }}</span>
                                        @endforeach
                                    @endforeach
                                @endif

                                <div class="form-group terms-conditions">
                                    <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }} data-msg="Данное поле обязательно" class="checkbox-template">
                                    <label for="remember">{{ __('Запомнить меня') }}</label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    {{ __('Войти') }}
                                </button>

                                @php
                                    $phone = \App\Settings::setting('phone');
                                    $telegram = \App\Settings::setting('telegram');
                                @endphp
                                <div class="row mx-1 mt-4 d-flex justify-content-between align-items-center">
                                    @if ($phone)
                                        <a href="tel:{{ preg_replace("/[^0-9]/", '', $phone) }}">{{ $phone }}</a>
                                    @endif
                                    @if ($telegram)
                                        <div class="">
                                            <div class="">
                                                <a href="{{ $telegram }}" target="_blank">
                                                Чат поддержки
                                                </a>
                                            </div>
                                            <div class="text-right">
                                                <a href="{{ $telegram }}" target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 50 50"
                                                         width="35px"
                                                         height="35px"
                                                    >
                                                        <path d="M46.137,6.552c-0.75-0.636-1.928-0.727-3.146-0.238l-0.002,0C41.708,6.828,6.728,21.832,5.304,22.445 c-0.259,0.09-2.521,0.934-2.288,2.814c0.208,1.695,2.026,2.397,2.248,2.478l8.893,3.045c0.59,1.964,2.765,9.21,3.246,10.758 c0.3,0.965,0.789,2.233,1.646,2.494c0.752,0.29,1.5,0.025,1.984-0.355l5.437-5.043l8.777,6.845l0.209,0.125 c0.596,0.264,1.167,0.396,1.712,0.396c0.421,0,0.825-0.079,1.211-0.237c1.315-0.54,1.841-1.793,1.896-1.935l6.556-34.077 C47.231,7.933,46.675,7.007,46.137,6.552z M22,32l-3,8l-3-10l23-17L22,32z"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
