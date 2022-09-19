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
        <div class="row d-flex justify-content-center">
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
