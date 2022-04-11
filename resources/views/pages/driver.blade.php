@extends('layouts.app')

@section('title', 'ЛК Водителя')

@section('content')
    <div class="container text-center">
        <h1>Здравствуйте, {{ auth()->user()->name }}!</h1>
        <hr>

        <div class="row">
            <div class="col-md-12 mb-2">
                <a href="{{ route('page.driver_bdd') }}" class="w-100 btn btn-default">Пройти инструктаж по безопасности дорожного движения</a>
            </div>
            <div class="col-md-12 mb-2">
                <a href="https://transavto7.ru/ostavit-otzyv" target="_blank" class="btn btn-lg btn-default w-100">Оставить отзыв или жалобу</a>
            </div>
            <div class="col-md-12 mb-2">
                <a href="https://t.me/transavto7_bot" target="_blank" class="btn btn-lg btn-default w-100">Чат поддержки</a>
            </div>
            <div class="col-md-12 mb-2">
                <a href="https://transavto7.ru/blog/instrukciya-dlya-voditelya-stavshego-uchastnikom-dtp" target="_blank" class="btn btn-lg btn-default w-100">Произошло ДТП?</a>
            </div>
            <div class="col-md-12 mb-2">
                <a target="_blank" href="https://transavto7.ru/contacts" class="btn btn-lg btn-default w-100">Карта пунктов выпуска Трансавто-7</a>
            </div>
        </div>

    </div>
@endsection
