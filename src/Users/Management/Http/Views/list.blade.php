@php
    /** @var \Src\Users\Management\Queries\GetUsersListPage\UsersListPage $page */
@endphp

@extends('layouts.app')

@section('title', 'Пользователи')
@section('sidebar', 1)

@section('content')
    <users-index-widget></users-index-widget>
@endsection

@push('setup-scripts')
    <script>
      window.PAGE_SETUP = {
        permissions: {
          canRead: @json($page->isCanRead()),
          canPasswordChange: @json($page->isCanPasswordChange()),
          canBlock: @json($page->isCanBlock()),
          canReadLogs: @json($page->isCanReadLogs()),
        },
        statusFilterOptions: @json($page->getStatusFilterOptions()),
        entityTypeFilterOptions: @json($page->getEntityTypeFilterOptions()),

        // logs
        LOGS_MODAL: {
          tableDataUrl: '{{ route('logs.list-model') }}',
          mapDataUrl: '{{ route('logs.list-model-map') }}',
          model: '{{ 'users' }}',
        },
        MODEL_SEARCHER: {
          tableDataUrl: '{{ route('searchElement') }}',
        }
      }
    </script>
@endpush