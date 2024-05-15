@extends('layouts.app')

@section('title', 'Журнал логирования')
@section('sidebar', 1)

@section('content')
    <logs-index />
@endsection

@push('setup-scripts')
    <script>
        @php
            $testOptions = [
                [ 'id' => '1', 'text' => 'First' ],
                [ 'id' => '2', 'text' => 'Second' ],
                [ 'id' => '3', 'text' => 'Third' ],
            ];
        @endphp

        window.PAGE_SETUP.usersOption = @json($testOptions);
        window.PAGE_SETUP.modelsOption = @json($testOptions);
        window.PAGE_SETUP.actionsOption = @json($testOptions);
        window.PAGE_SETUP.tableDataUrl = '/api/logs';
    </script>
@endpush


