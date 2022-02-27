@extends('layouts.app')

@section('title', 'ЛК Водителя')

@section('content')
    <div class="container text-center">
        <h1>Здравствуйте, {{ auth()->user()->getName() }}!</h1>
        <hr>

        <div class="row">
            <div class="col-md-12 mb-2">
                <a href="{{ route('page.driver_bdd') }}" class="w-100 btn btn-default">БДД</a>
            </div>
            <div class="col-md-12 mb-2">
                <a href="" class="btn btn-lg btn-default w-100">Информация</a>
            </div>
            <div class="col-md-12 mb-2">
                <a href="" class="btn btn-lg btn-default w-100">ДТП?</a>
            </div>
            <div class="col-md-12 mb-2">
                <a target="_blank" href="https://yandex.ru/maps/?from=api-maps&ll=36.887406%2C45.239326&mode=usermaps&origin=jsapi_2_1_79&um=constructor%3Abd8e97b601c8606a0824196d78c94c5fef385825da36549b56470624bc9b086c&z=6" class="btn btn-lg btn-default w-100">Карта</a>
            </div>
        </div>

    </div>
@endsection
