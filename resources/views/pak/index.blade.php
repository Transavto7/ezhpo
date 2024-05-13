@extends('layouts.app')

@section('title', 'Очередь на утверждение')
@section('sidebar', 1)

@section('custom-styles')
    <style>
        .table-card {
            max-height: 65vh;
            overflow: hidden;
        }

        .table-card > .card-body {
            overflow: scroll;
            padding: 0 !important;
            margin: 15px !important;
            overscroll-behavior: contain;
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div class="card mb-0">
            <div class="card-body pb-0">
                @if(user()->access('approval_queue_clear'))
                    <a href="?clear=1" class="btn btn-warning">Очистить очередь</a>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger mt-2 mb-0" role="alert">{{ session()->get('error') }}</div>
                @endif
            </div>
        </div>

        <div class="card table-card">
            <div class="card-body">
                <pak-index
                    :fields='@json($fields)'
                    time="{{ \Carbon\Carbon::now() }}"
                    :reload-interval="1000" />
            </div>
        </div>
    </div>
@endsection
