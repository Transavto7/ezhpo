@extends('layouts.app')

@section('title', 'Отчет по услугам компании')
@section('sidebar', 1)

@section('content')
    @if(user()->access('report_service_company_read', 'report_service_company_export'))
        <report-journal-index
        @if ($company)
            :default_company="{{ json_encode($company) }}"
        @endif

        @if (user()->isCompany())
            :client_company="{{ json_encode(auth()->user()->relatedCompany->only('hash_id', 'name', 'inn')) }}"
       @endif
        :permissions='@json([
            'create' => user()->access('report_service_company_read'),
            'export' => user()->access('report_service_company_export'),
        ])'
    ></report-journal-index>
    @endif
@endsection
