@extends('layouts.app')

@section('title', 'ЛК Водителя')

@section('content')
    <div class="container text-center">
        <h1>Здравствуйте!</h1>
        <hr>

        <div class="row">
            <div class="col-md-3">
                <a href="{{ route('page.driver_bdd') }}" class="btn btn-lg btn-info">БДД</a>
            </div>
            <div class="col-md-3">
                <a href="" class="btn btn-lg btn-info">Информация</a>
            </div>
            <div class="col-md-3">
                <a href="" class="btn btn-lg btn-info">ДТП?</a>
            </div>
            <div class="col-md-3">
                <a href="" class="btn btn-lg btn-info">Карта</a>
            </div>
        </div>

    </div>
@endsection
