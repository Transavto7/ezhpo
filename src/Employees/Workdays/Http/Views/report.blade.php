@php
    /** @var array<string> $months */
    /** @var array<int> $years */
    /** @var int $selectedMonth */
    /** @var int $selectedYear */
    /** @var array<array> $townList */
    /** @var array<array> $roleList */
    /** @var array<array> $pointList */
    /** @var array<array> $employeeList */
@endphp

@extends('layouts.app')

@section('title', 'Расчет ЗП')
@section('sidebar', 1)

@section('content')
    <user-report-widget></user-report-widget>
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP = {
            months: @json($months),
            years: @json($years),
            selectedMonth: @json($selectedMonth),
            selectedYear: @json($selectedYear),
            townList: @json($townList),
            roleList: @json($roleList),
            pointList: @json($pointList),
            employeeList: @json($employeeList)
        }
    </script>
@endpush
