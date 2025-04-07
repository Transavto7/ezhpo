@extends('layouts.app')

@section('title', 'Список тарифов')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
        {{--window.PAGE_SETUP.holidays = @json($holidays);--}}
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="row bg-light p-2 mb-2">
            <div class="m-2">
                <a class="btn btn-sm btn-success" href="{{ route('employees.tariffs.create-page') }}">
                    Добавить <i class="fa fa-plus"></i></a>
            </div>
        </div>
        <div class="card">
            <div class="col-md-12 p-4">
                <tariffs-list-widget/>
            </div>
        </div>
@endsection
