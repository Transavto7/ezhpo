@extends('layouts.app')

@section('title', 'Создание напоминания сотруднику')
@section('sidebar', 1)

@push('setup-scripts')
    <script src="{{ asset("libs/tinymce/tinymce.min.js")}}"></script>
    <script>
    </script>
@endpush

@section('content')
    <div class="col-md-12">
            <create-reminder-widget/>
    </div>
@endsection
