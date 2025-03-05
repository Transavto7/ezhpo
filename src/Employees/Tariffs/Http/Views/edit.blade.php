@php /** @var \Src\Employees\Tariffs\Queries\GetTariff\TariffViewModel $tariff */ @endphp
@extends('layouts.app')

@section('title', 'Редактирование тарифа')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.tariff = @json($tariff->toArray());
    </script>
@endpush

@section('content')
    <div class="col-md-12">
            <edit-tariff-widget/>
    </div>
@endsection
