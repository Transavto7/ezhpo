@extends('layouts.app')

@section('title', 'Отказы СДПО')
@section('sidebar', 1)

@section('content')
    <sdpo-crash-logs-index />
@endsection

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.versionsOptions = @json($versions);
        window.PAGE_SETUP.typesOptions = @json($types);
        window.PAGE_SETUP.typesOptions = @json($types);
        window.PAGE_SETUP.typesMap = @json(array_column($types, 'text', 'id'));
        window.PAGE_SETUP.terminalsOptions = @json($terminals);
        window.PAGE_SETUP.pointsOptions = @json($points);
        window.PAGE_SETUP.tableDataUrl = '/admin/sdpo-crash-logs/list';
    </script>
@endpush


