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
            <div class="col-md-3">
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src="{{ $user_avatar }}"
                         alt="Card image cap">

                    <div class="">

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">ФИО:</span>
                            </div>
                            <input value="{{user()->name}}" disabled type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">E-mail:</span>
                            </div>
                            <input value="{{user()->email}}" disabled type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">Должность:</span>
                            </div>
                            <input value="Водитель" disabled type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                        </div>
                        {{--                        <p>Диспетчер</p>--}}
                    </div>
                </div>
            </div>
            <div class="col-md-9">

                <div class="container overflow-hidden">

                    <div class=" row gy-5 gx-5 ">
                        <div class="col-6 my-2">
                            <a style="width: 100%" href="{{ route('page.driver_bdd') }}">
                                <div class="menu_link_driver">
                                    Пройти инструктаж по безопасности дорожного движения
                                </div>
                            </a>
                        </div>
                        <div class=" col-6 my-2">
                            <a style="width: 100%" href="https://transavto7.ru/ostavit-otzyv" target="_blank"
                               >
                                <div class="menu_link_driver">
                                    Оставить отзыв или жалобу
                                </div>
                            </a>
                        </div>
                        <div class=" col-6 my-2">
                            <a style="width: 100%" href="https://t.me/transavto7_bot" target="_blank"
                               >
                                <div class="menu_link_driver">
                                    Чат поддержки
                                </div>
                            </a>
                        </div>
                        <div class=" col-6 my-2">
                            <a style="width: 100%"
                               href="https://transavto7.ru/blog/instrukciya-dlya-voditelya-stavshego-uchastnikom-dtp"
                               target="_blank">
                                <div class="menu_link_driver">
                                    Произошло ДТП?
                                </div>
                            </a>
                        </div>
                        <div class="col my-2">
                            <a style="width: 100%" target="_blank" href="https://transavto7.ru/contacts">
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

@section('custom-styles')
    <style>
        .menu_link_driver {
            border-radius: 20px;
            border-color: black;
            background-color: #4aa0e6;
            color: white;
            height: 150px;
            font-size: 26px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .menu_link_driver a {
            background: none;
        }

        main{
            background-color: #BACEF4;
        }

    </style>
@endsection
