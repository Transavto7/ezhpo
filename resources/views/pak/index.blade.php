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
                    <button class="btn btn-warning" id="clearQueueBtn">Очистить очередь</button>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mt-2 mb-0">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
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

@section('custom-scripts')
    <script type="text/javascript">
        console.log("{{ route('pak.index', ['clear' => 1]) }}")

        $('#clearQueueBtn').on('click', function (event) {
            window.swal.fire({
                title: 'Очистка очереди!',
                text: 'Перевести все осмотры в режим СДПО-А и принять решение о допуске автоматически?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Очистить',
                cancelButtonText: "Отмена",
            }).then(function (result) {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('pak.clear') }}"
                }
            })
        })
    </script>
@endsection
