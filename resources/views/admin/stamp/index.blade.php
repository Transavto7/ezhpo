@extends('layouts.app')

@section('title', 'Штампы')
@section('sidebar', 1)
@section('content')
    @php
        $current_user_permissions = [
            'permission_to_edit' => user()->access('stamp_edit'),
            'permission_to_create' => user()->access('stamp_create'),
            'permission_to_delete' => user()->access('stamp_delete'),
            'permission_to_trash' => user()->access('stamp_trash'),
            'permission_to_create' => user()->access('stamp_create'),
        ];
    @endphp

    <admin-stamp-index
        :permissions='@json($current_user_permissions)'
        :fields='@json($fields)'
    />
@endsection
