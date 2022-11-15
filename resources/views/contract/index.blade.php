@extends('layouts.app')

@section('title', 'Договоры')
@section('sidebar', 1)
@php
    $permissions = [
            'create' => user()->access('contract_create'),
            'trash' => user()->access('contract_trash'),
            'read' => user()->access('contract_read'),
            'delete' => user()->access('contract_delete'),
            'edit' => user()->access('contract_edit'),
        ];
@endphp
@section('content')
    <contract-index
        :permissions='@json($permissions)'
    >
    </contract-index>
@endsection
