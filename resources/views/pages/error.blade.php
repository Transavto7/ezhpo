@extends('layouts.app')

@section('title', 'Ошибка! Упс')
@section('class-page', 'page--error')

@section('content')
    <div class="container text-center">
        <img src="{{ asset('images/error.png') }}" width="80%" alt="" />
    </div>
@endsection
