@extends('layouts.app')

@section('title', 'Список напоминаний сотрудникам')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="row bg-light p-2 mb-2">
            <div class="m-2">
                <a class="btn btn-sm btn-success" href="{{ route('reminders.create-page') }}">
                    Добавить <i class="fa fa-plus"></i></a>
            </div>
        </div>
        <div class="card">
            <div class="col-md-12 p-4">
                <reminders-list-widget/>
            </div>
        </div>
@endsection
