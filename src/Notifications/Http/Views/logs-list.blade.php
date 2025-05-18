@extends('layouts.app')

@section('title', 'Журнал действий с уведомлениями')
@section('sidebar', 1)

@section('content')
    <notification-logs-list-widget></notification-logs-list-widget>
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.canEmployeeRead = @json($canEmployeeRead);
    </script>
@endpush
