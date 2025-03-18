@extends('layouts.app')

@section('title', 'Журнал рабочих смен сотрудников')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
        window.PAGE_SETUP.holidays = @json($holidays);
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        <div class="card">
            <holiday-index/>
        </div>
    </div>
@endsection
