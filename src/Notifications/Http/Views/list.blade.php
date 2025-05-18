@extends('layouts.app')

@section('title', 'Список уведомлений')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.canViewOther = @json($canViewOther);
        window.PAGE_SETUP.canChangeOther = @json($canChangeOther);
        window.PAGE_SETUP.canEmployeeRead = @json($canEmployeeRead);
        window.PAGE_SETUP.canRemindersRead = @json($canRemindersRead);
        window.PAGE_SETUP.statusOptions = @json($statusOptions);
    </script>
@endpush

@section('content')
    <notifications-list-widget></notifications-list-widget>
@endsection
