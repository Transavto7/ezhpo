@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)
@php

    $permissionEdit = user()->access('pak_sdpo_update');
    $permissionView = user()->access('pak_sdpo_read');
    $permissionDelete = user()->access('pak_sdpo_delete');
    $permissionCreate = user()->access('pak_sdpo_create');
    $permissionTrash = user()->access('pak_sdpo_trash');
    $permissionExport = user()->access('pak_sdpo_export');

@endphp

@section('content')

    @include('admin.users.users_add_modal')

    <div class="col-md-12">
        <div class="row bg-light p-2">
            <div class="col">
                @if($permissionCreate)
                <button type="button" data-toggle="modal" data-target="#users-modal-add" class="btn btn-success">Добавить {{ $is_pak ? 'терминал' : 'сотрудника' }} <i class="fa fa-plus"></i></button>
                @endif
                @if($permissionTrash)
                    @if(request()->get('deleted'))
                        <a href="{{ route('adminUsers', [
                                'filter' => 1,
                                'pak_sdpo' => 1
                            ]) }}" class="btn btn-warning btn-sm">Назад</a>
                    @else
                        <a href="{{ route('adminUsers', [
                                'filter' => 1,
                                'pak_sdpo' => 1,
                                'deleted' => 1
                            ]) }}" class="btn btn-warning btn-sm">Корзина</a>
                    @endif
                @endif
            </div>
            <div class="col text-right">
                @if($permissionExport)
                <button type="button" onclick="exportTable('elements-table', '{{ $title }}', '{{ $title }}.xls')" class="btn btn-dark">Экспорт <i class="fa fa-download"></i></button>
                @endif
            </div>
        </div>
        @if($permissionView)
        <div class="row bg-light p-2">
            <div class="col-md-12">
                <form action="" class="row" method="GET">
                    @csrf
                    <input type="hidden" name="filter" value="1" />

                    <div class="col-md-2 form-group">
                        <input type="text" value="{{ request()->get('name') }}" name="name" placeholder="ФИО" class="form-control">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="text" name="email" value="{{ request()->get('email') }}" placeholder="E-mail" class="form-control">
                    </div>

                    <div class="col-md-2 form-group">
                        @include('profile.ankets.components.pvs', [
                            'defaultShowPvs' => 1,
                            'classesPvs' => 'form-control'
                        ])
                    </div>

                    <div class="col-md-3 form-group">
                        <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                        <a href="{{ route('adminUsers') }}" class="btn btn-danger btn-sm">Сбросить</a>
                    </div>

                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="card">
        @if($errors)
            @foreach($errors as $error)
                <p class="alert alert-danger"><b>{{ $error[0] }}</b></p>
            @endforeach
        @endif

        @if($permissionView)
            @include('admin.users.users_table')

            <div class="col-md-12">
                {{ $users->appends($_GET)->render() }}

                <p>Количество элементов: {{ count($users) }}</p>
            </div>
        @endif

    </div>

@endsection
