@extends('layouts.app')

@section('title', 'Договоры')
@section('sidebar', 1)
@section('content')
    <contract-index
        :permissions='@json($permissions)'
        :fields='@json($fields)'
    >
    </contract-index>
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.LOGS_MODAL = {
            tableDataUrl: '{{ route('logs.list-model') }}',
            mapDataUrl: '{{ route('logs.list-model-map') }}',
            model: '{{ 'contracts' }}',
        };
    </script>
@endpush
