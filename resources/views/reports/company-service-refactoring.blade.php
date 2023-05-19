@extends('layouts.app')

@section('title', 'Отчет по услугам компании новый внешний вид')
@section('sidebar', 1)

@section('content')
    <company-service-refactoring 
        @if (user()->hasRole('client'))
            :client_company="{{ json_encode(auth()->user()->company->only('hash_id', 'name')) }}"
        @endif
    >
        
    </company-service-refactoring>
@endsection
