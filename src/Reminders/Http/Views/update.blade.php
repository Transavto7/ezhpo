@php /** @var \Src\Reminders\Queries\GetReminderById\ReminderViewModel $reminder */ @endphp
@extends('layouts.app')

@section('title', 'Редактирование напоминания сотруднику')
@section('sidebar', 1)

@push('setup-scripts')
    <script src="{{ asset("libs/tinymce/tinymce.min.js")}}"></script>
    <script>
        window.PAGE_SETUP.reminder = @json($reminder->toArray());
    </script>
@endpush

@section('content')
    <div class="col-md-12">
            <edit-reminder-widget/>
    </div>
@endsection
