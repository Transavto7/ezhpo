@extends('layouts.app')

@section('title', 'Список напоминаний сотрудникам')
@section('sidebar', 1)

@push('setup-scripts')
    <script>
      window.PAGE_SETUP.canCreate = @json($canCreate);
      window.PAGE_SETUP.canEdit = @json($canEdit);
      window.PAGE_SETUP.canDelete = @json($canDelete);
    </script>
@endpush

@section('content')
    <div class="col-md-12">
        @if($canCreate)
            <div class="card mb-3 mb-2">
                <div class="card-body">
                    <a class="btn btn-sm btn-success" href="{{ route('reminders.create-page') }}">
                        Добавить <i class="fa fa-plus"></i></a>
                </div>
            </div>
        @endif

        <reminders-list-widget></reminders-list-widget>
    </div>
@endsection
