@php
    /** @var \Src\Users\Management\Queries\GetUserShowPage\UserShowPage $page */
@endphp

@extends('layouts.app')

@section('title', 'Пользователь')
@section('sidebar', 1)

@section('content')
    <user-show-widget></user-show-widget>
@endsection

@push('setup-scripts')
    <script>
      window.PAGE_SETUP = {
        id: @json($id),
        permissions: {
          canRead: @json($page->isCanRead()),
          canPasswordChange: @json($page->isCanPasswordChange()),
          canBlock: @json($page->isCanBlock()),
          canAccessChange: @json($page->isCanAccessChange()),
          canReadLogs: @json($page->isCanReadLogs()),
        },

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