@extends('layouts.app')

@section('title', 'Группы')
@section('sidebar', 1)
@php
//dd(\Spatie\Permission\Models\Permission::all());


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

        <admin-roles-index
            :roles='@json($roles)'
            :all_permissions='@json($all_permissions)'
        >

        </admin-roles-index>

    </div>

@endsection
