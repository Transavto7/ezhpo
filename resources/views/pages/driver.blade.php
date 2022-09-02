@extends('layouts.app')

@section('title', 'ЛК Водителя')

@section('content')
    @php
        if(user()->photo){
            $user_avatar = asset("storage/". user()->photo);
        }else{
            $user_avatar = asset("img/default_profile.jpg");
        }
    @endphp
    <div class="container text-center">
        <h1>Здравствуйте, {{ auth()->user()->name }}!</h1>
        <hr>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-2 driver-card">
                    <div class="p-4">
                        <img class="card-img-top driver-card-img" src="{{ $user_avatar }}"
                             alt="Card image cap">
                    </div>

                    <div>
                        <div class="input-group my-3 d-flex align-items-center">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="line-height: 1.3" id="inputGroup-sizing-default">ID:</span>
                            </div>
                            <input value="{{ user()->hash_id }}" disabled type="text" class="form-control p-3 fw-bold" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>

                        <div class="input-group my-3 d-flex align-items-center">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="line-height: 1.3" id="inputGroup-sizing-default">ФИО:</span>
                            </div>
                            <input value="{{ user()->name }}" disabled type="text" class="form-control p-3 fw-bold" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>

                        <div class="input-group mb-3 d-flex align-items-center">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="line-height: 1.3">Должность:</span>
                            </div>
                            <input value="Водитель" disabled type="text" class="form-control p-3 fw-bold" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>

                        <div class="input-group mb-3 d-flex align-items-center">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="line-height: 1.3">Пункт выпуска:</span>
                            </div>
                            <input value="{{ user()->pv->name ?? 'Неизвестно' }}" disabled type="text"
                                   class="form-control p-3 fw-bold" aria-label="Default"
                                   aria-describedby="inputGroup-sizing-default">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">

                <div class="container overflow-hidden">

                    <div class="row row-flex">
                        <div class="col-lg-6 my-2">
                            <a class="text-decoration-none" href="{{ route('page.driver_bdd') }}">
                                <div class="menu_link_driver">
                                    Пройти инструктаж по безопасности дорожного движения
                                </div>
                            </a>
                        </div>
                        <div  class="col-lg-6 my-2">
                            <a class="text-decoration-none" style="width: 100%" href="https://transavto7.ru/ostavit-otzyv" target="_blank"
                               >
                                <div class="menu_link_driver">
                                    Оставить отзыв или жалобу
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class=" col-6 my-2">
                            <a class="text-decoration-none" style="width: 100%" href="https://t.me/transavto7_bot" target="_blank"
                               >
                                <div class="menu_link_driver">
                                    Чат поддержки
                                </div>
                            </a>
                        </div>
                        <div class=" col-6 my-2">
                            <a class="text-decoration-none" style="width: 100%"
                               href="https://transavto7.ru/blog/instrukciya-dlya-voditelya-stavshego-uchastnikom-dtp"
                               target="_blank">
                                <div class="menu_link_driver">
                                    Произошло ДТП?
                                </div>
                            </a>
                        </div>
                        <div class="col my-2">
                            <a class="text-decoration-none" style="width: 100%" target="_blank" href="https://transavto7.ru/contacts">
                                <div class="menu_link_driver">
                                    Карта пунктов выпуска Трансавто-7
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
