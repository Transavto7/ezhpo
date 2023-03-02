@extends('layouts.app')

@section('title', 'Настройки левого меню')
@section('sidebar', 1)
@section('content')
    <admin-sidebar-index
            :sidebarItems='@json($items)'
    ></admin-sidebar-index>
@endsection
