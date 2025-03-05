@extends('layouts.app')

@section('title', 'Создание тарифа')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
        {{--window.PAGE_SETUP.holidays = @json($holidays);--}}
    </script>
@endpush

@section('content')
    <div class="col-md-12">
            <create-tariff-widget/>
    </div>
@endsection
