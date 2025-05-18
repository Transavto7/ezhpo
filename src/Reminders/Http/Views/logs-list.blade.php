@extends('layouts.app')

@section('title', 'Журнал действий с напоминаниями')
@section('sidebar', 1)

@section('content')
    <reminder-logs-list-widget></reminder-logs-list-widget>
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.canEmployeeRead = @json($canEmployeeRead);
        window.PAGE_SETUP.canRemindersRead = @json($canRemindersRead);
    </script>
@endpush
