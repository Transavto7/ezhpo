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

                            <div class="row mx-1 mt-4 d-flex justify-content-between">
                                <div class="d-flex flex-column align-items-end">
                                    <a href="tel:88005507077">8 (800) 550-70-77</a>
                                    <a class="mt-2" href="tel:79787775555">+7 (978) 777-55-55</a>
                                </div>
                                <a href="https://t.me/transavto7_bot">Чат в Telegram</a>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
