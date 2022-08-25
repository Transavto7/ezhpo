@extends('layouts.app')

@section('title', 'ЛК Водителя')

@section('content')
    @php
        //echo asset('storage/file.txt');
        //    dump(
        //auth()->user()->toArray(),
        //\Illuminate\Support\Facades\Storage::disk('public')->url('app/public/' . auth()->user()->photo),
         //asset(auth()->user()->photo),

         //asset("storage/app/public/". auth()->user()->photo ),
         //'storage/app/public/elements/abccc3fab020169a1153af6f4f84e0b7b8f851bc.png',
         //Storage::disk('public')->url('elements/abccc3fab020169a1153af6f4f84e0b7b8f851bc.png')

    //  )
    @endphp
    <div class="container text-center">
        <h1>Здравствуйте, {{ auth()->user()->name }}!</h1>
        <hr>

        <div class="row">
            <div class="col-md-3">
                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src="{{ asset("storage/". auth()->user()->photo ) }}"
                         alt="Card image cap">

                    <div class="">
                        <p>{{ user()->name }}</p>
                        <p>{{ user()->email }}</p>
                        <p>Должность - Водитель</p>
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

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .menu_link_driver a {
            background: none;
        }

    </style>
@endsection
