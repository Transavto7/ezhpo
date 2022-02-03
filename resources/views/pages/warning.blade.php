@extends('layouts.app')

@section('title', 'Ошибка!')
@section('class-page', 'page--success')

@section('content')
    <div class="container text-center">
        <h1 class="text-warning">{{ $text }}</h1>
        <hr>
        <a href="{{ $_SERVER['HTTP_REFERER'] }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Вернуться назад</a>
    </div>
@endsection
