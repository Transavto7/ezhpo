@extends('layouts.app')

@section('title', 'Группы')
@section('sidebar', 1)
@php
//dd(\Spatie\Permission\Models\Permission::all());

    $current_user_permissions = [
            'permission_to_edit' => user()->access('group_update'),
            'permission_to_view' => user()->access('group_read'),
            'permission_to_create' => user()->access('group_create'),
            'permission_to_delete' => user()->access('group_delete'),
            'permission_to_trash' => user()->access('group_trash'),
        ];

//dd($all_permissions->pluck('guard_name')->toArray());
//foreach(config('access') as $permission){
//    \Spatie\Permission\Models\Permission::where('name', $permission['name'])->update(['guard_name' => $permission['description']]);
//}
//dd(\Spatie\Permission\Models\Role::all());
//$role = \Spatie\Permission\Models\Role::with(['permissions'])->find(1);
//dd($role->toArray());
//$role->permissions()->attach([1,2,3]);
@endphp
@section('content')

    <div class="col-md-12">

        <div class="col-md-4 m-2">
            @if($current_user_permissions['permission_to_trash'])
                @isset($_GET['deleted'])
                    <a href="/roles" class="btn btn-sm btn-warning">Назад</a>
                @else
                    <a href="?deleted=1" class="btn btn-sm btn-warning">Корзина <i class="fa fa-trash"></i></a>
                @endisset
            @endif
        </div>

        <admin-roles-index
            :roles='@json($roles)'
            :deleted='{{ request()->get('deleted', 0) }}'
            :current_user_permissions='@json($current_user_permissions)'
            :all_permissions='@json($all_permissions)'
        >

        </admin-roles-index>

    </div>

@endsection
