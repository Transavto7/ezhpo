@extends('layouts.app')

@section('title', 'Очередь на утверждение')
@section('sidebar', 1)
@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                    {{-- ОЧИСТКА ОЧЕРЕДИ СДПО --}}
                    @if(user()->access('approval_queue_clear'))
                        <a href="?clear=1" class="btn btn-warning mb-2">Очистить очередь</a>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session()->get('error') }}</div>
                    @endif

                <pak-index
                    :fields='@json($fields)'
                    time="{{ \Carbon\Carbon::now() }}"
                    :reload-interval="1000" />
            </div>
        </div>
    </div>
@endsection
