@extends('layouts.app')

@section('title', 'Договоры')
@section('sidebar', 1)
@section('content')
    <contract-index
        :permissions='@json($permissions)'
        :fields='@json($fields)'
    >
    </contract-index>
@endsection
