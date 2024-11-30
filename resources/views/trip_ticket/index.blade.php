@extends('layouts.app')

@section('title', 'Реестр путевых листов')
@section('sidebar', 1)

@section('content')
    <trip-ticket-index />
@endsection

@push('setup-scripts')
    <script>
        {{--window.PAGE_SETUP.usersOption = @json($users);--}}

        {{--window.PAGE_SETUP.modelsOption = @json($modelTypes);--}}
        {{--window.PAGE_SETUP.modelsMap = @json(array_column($modelTypes, 'text', 'id'))--}}

        {{--window.PAGE_SETUP.actionsOption = @json($actionTypes);--}}
        {{--window.PAGE_SETUP.actionsMap = @json(array_column($actionTypes, 'text', 'id'))--}}

        {{--window.PAGE_SETUP.fieldPromptsMap = @json($fieldPromptsMap);--}}

        window.PAGE_SETUP.tableDataUrl = '/trip-ticket/list';
    </script>
@endpush


