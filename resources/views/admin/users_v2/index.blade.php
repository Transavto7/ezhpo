@extends('layouts.app')

@section('title', 'Сотрудники')
@section('sidebar', 1)
@php
    $points = \App\Town::with(['pvs'])->get();
    $roles = \Spatie\Permission\Models\Role::all();
    $all_permissions = \Spatie\Permission\Models\Permission::orderBy('guard_name')->get();

    $current_user_permissions = [
            'permission_to_edit' => user()->access('employee_update'),
            'permission_to_view' => user()->access('employee_read'),
            'permission_to_create' => user()->access('employee_create'),
            'permission_to_delete' => user()->access('employee_delete'),
            'permission_to_trash' => user()->access('employee_trash'),
        ];
    //dd($current_user_permissions);
    //dd($current_user_permissions
    $permissionToTrashView  = user()->access('employee_trash');
  //  dd(request()->get('deleted'))
@endphp
@section('content')

    <div class="col-md-12">

        <div class="card">

            {{--            @if($permissionToTrashView)--}}
            {{--                <div class="m-2">--}}
            {{--                    @if(!request()->get('deleted'))--}}
            {{--                        <a href="?deleted=1" class="btn btn-sm btn-warning">--}}
            {{--                            Удалённые <i class="fa fa-trash"></i>--}}
            {{--                        </a>--}}
            {{--                    @else--}}
            {{--                        <a href="{{ route('users') }}" class="btn btn-sm btn-warning">--}}
            {{--                            Назад <i class="fa fa-trash"></i>--}}
            {{--                        </a>--}}
            {{--                    @endif--}}
            {{--                </div>--}}
            {{--            @endif--}}

            <div class="card-body">
                <div class="col-md-4 m-2">
                    @if($permissionToTrashView)
                        @isset($_GET['deleted'])
                            <a href="{{ route('users') }}" class="btn btn-sm btn-warning">Назад</a>
                        @else
                            <a href="?deleted=1" class="btn btn-sm btn-warning">Корзина <i class="fa fa-trash"></i></a>
                        @endisset
                    @endif
                </div>

                @if($current_user_permissions['permission_to_view'])
                    <form action="" class="row" method="GET">

                        @isset($_GET['deleted'])
                            <input type="hidden" value="1" name="deleted">
                        @endif
                        <div class="col-md-2 form-group">
                            <input type="text" value="{{ request()->get('name') }}" name="name" placeholder="ФИО"
                                   class="form-control">
                        </div>

                        <div class="col-md-2 form-group">
                            <input type="text" name="email" value="{{ request()->get('email') }}" placeholder="E-mail"
                                   class="form-control">
                        </div>

                        <div class="col-md form-group">
                            @include('profile.ankets.components.pvs', [
                                'defaultShowPvs' => 1,
                                'classesPvs' => 'form-control',
                                'points' => $points->toArray(),
                                'roles' => $roles
                            ])
                        </div>

                        <div class="col-md form-group">
                            <input type="submit" class="btn btn-success btn-sm" value="Поиск">
                            <a href="{{ route('users') }}" class="btn btn-danger btn-sm">Сбросить</a>
                        </div>

                    </form>
                @endif
            </div>
        </div>
        <admin-users-index
            :roles='@json($roles)'
            :deleted='{{ request()->get('deleted', 0) }}'
            :current_user_permissions='@json($current_user_permissions)'
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

{{--        <div class="col-md-12">--}}
{{--            {{ $users->appends($_GET)->render() }}--}}

{{--            <p>Количество элементов: {{ count($users) }}</p>--}}
{{--        </div>--}}
    </div>

@endsection
