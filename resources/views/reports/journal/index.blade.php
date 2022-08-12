@extends('layouts.app')

@section('title', 'Отчет по услугам компании')
@section('sidebar', 1)

@section('content')
    <report-journal-index
        @if ($company)
            :default_company="{{ json_encode($company) }}"
        @endif

        @if (auth()->user()->role == \App\User::$userRolesValues['client'])
            :client_company="{{ json_encode(auth()->user()->company->only('hash_id', 'name')) }}"
       @endif
    ></report-journal-index>
@endsection
