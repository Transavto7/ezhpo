@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@section('content')
    <div class="row">
        <div class="col-md-4">
            <h4>Компания</h4>

            <iframe id="COMPANY_FRAME" src="{{ route('renderElements', [
                'type' => 'Company',
                'continue' => 1
            ]) }}" width="100%" height="700" frameborder="0"></iframe>

            <div class="text-right">
                <a href="#" class="btn btn-primary" onclick="COMPANY_FRAME.contentWindow.location.reload()">Обновить</a>
            </div>
        </div>
        <div class="col-md-4">
            <h4>Водитель</h4>

            <iframe id="DRIVERS_FRAME" src="{{ route('renderElements', [
                'type' => 'Driver',
                'continue' => 1
            ]) }}" width="100%" height="700" frameborder="0"></iframe>

            <div class="text-right">
                <a href="#" class="btn btn-primary" onclick="DRIVERS_FRAME.contentWindow.location.reload()">Обновить</a>
            </div>
        </div>
        <div class="col-md-4">
            <h4>Автомобиль</h4>

            <iframe id="CARS_FRAME" src="{{ route('renderElements', [
                'type' => 'Car',
                'continue' => 1
            ]) }}" width="100%" height="700" frameborder="0"></iframe>

            <div class="text-right">
                <a href="#" class="btn btn-primary" onclick="CARS_FRAME.contentWindow.location.reload()">Обновить</a>
            </div>
        </div>
    </div>
@endsection
