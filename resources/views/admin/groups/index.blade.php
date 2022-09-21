@extends('layouts.app')

@section('title', 'Роли')
@section('sidebar', 1)
@php
    $current_user_permissions = [
            'permission_to_edit' => user()->access('group_update'),
            'permission_to_view' => user()->access('group_read'),
            'permission_to_create' => user()->access('group_create'),
            'permission_to_delete' => user()->access('group_delete'),
            'permission_to_trash' => user()->access('group_trash'),
        ];
@endphp
@section('content')

    <div class="col-md-12">

        <admin-roles-index
            :roles='@json($roles)'
            :deleted='{{ request()->get('deleted', 0) }}'
            :current_user_permissions='@json($current_user_permissions)'
            :all_permissions='@json($all_permissions)'
            :fields='@json($fields)'
        >

        </admin-roles-index>

    </div>

@endsection
