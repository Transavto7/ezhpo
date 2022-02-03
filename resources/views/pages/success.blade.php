@extends('layouts.app')

@section('title', 'Успешно!')
@section('class-page', 'page--success')

@section('content')
    <div class="container text-center">
        <h1 class="text-success">{{ $text }}</h1>
        <hr>
        <a href="{{ $_SERVER['HTTP_REFERER'] }}" class="btn btn-success"><i class="fa fa-arrow-left"></i> Вернуться назад</a>
    </div>
@endsection
