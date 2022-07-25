@extends('layouts.app')

@section('title', 'Отчет по услугам компании')
@section('sidebar', 1)

@section('content')
    <report-journal-index
        @if ($company)
            :default_company="{{ json_encode($company) }}"
        @endif
    ></report-journal-index>
@endsection
