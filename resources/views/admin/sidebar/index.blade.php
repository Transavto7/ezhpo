@extends('layouts.app')

@section('title', 'Настройки левого меню')
@section('sidebar', 1)
@section('content')
    <admin-sidebar-index
            :sidebaritems='@json($sidebarItems->toArray())'
            :headitems='@json($headers->toArray())'
    ></admin-sidebar-index>
@endsection
