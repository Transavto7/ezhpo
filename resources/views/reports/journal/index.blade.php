@extends('layouts.app')

@section('title', 'Отчет по услугам компании новый')
@section('sidebar', 1)

@section('content')
    @if(user()->access('report_service_company_read', 'report_service_company_export'))
        <report-journal-index
        @if ($company)
            :default_company="{{ json_encode($company) }}"
        @endif

        @if (user()->hasRole('client'))
            :client_company="{{ json_encode(auth()->user()->company->only('hash_id', 'name')) }}"
       @endif
        :permissions='@json([
            'create' => user()->access('report_service_company_read'),
            'export' => user()->access('report_service_company_export'),
        ])'
    ></report-journal-index>
    @endif
@endsection
