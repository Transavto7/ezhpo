@extends('layouts.app')

@section('title', 'Сотрудники')
@section('sidebar', 1)
@php
$points = \App\Town::with(['pvs'])->get();
$roles = \Spatie\Permission\Models\Role::all();
$all_permissions = \Spatie\Permission\Models\Permission::all();
@endphp
@section('content')

    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <form action="" class="row" method="GET">

                    <div class="col-md-2 form-group">
                        <input type="text" value="{{ request()->get('name') }}" name="name" placeholder="ФИО" class="form-control">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="text" name="email" value="{{ request()->get('email') }}" placeholder="E-mail" class="form-control">
                    </div>

                    <div class="col-md-2 form-group">
                        @include('profile.ankets.components.pvs', [
                            'defaultShowPvs' => 1,
                            'classesPvs' => 'form-control',
                            'points' => $points->toArray(),
                            'roles' => $roles
                        ])
                    </div>

                    <div class="col-md-3 form-group">
                        <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                        <a href="{{ route('users') }}" class="btn btn-danger btn-sm">Сбросить</a>
                    </div>

                </form>
            </div>
        </div>
        <admin-users-index
            :users='@json($users->getCollection())'
            :roles='@json($roles)'
            :all_permissions='@json($all_permissions)'
            :points='@json($points->map(function ($q){
                                        $res['label'] = $q->name;
                                        foreach ($q->pvs as $pv){
                                            $res['options'][] = ['value' => $pv['id'], 'text' => $pv['name']];
                                        }
                                        return $res;
                                        }))'
        >

        </admin-users-index>

        <div class="col-md-12">
            {{ $users->appends($_GET)->render() }}

            <p>Количество элементов: {{ count($users) }}</p>
        </div>
    </div>

@endsection
