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
                <a href="" class="btn btn-lg btn-default w-100">Карта</a>
            </div>
        </div>

    </div>
@endsection
