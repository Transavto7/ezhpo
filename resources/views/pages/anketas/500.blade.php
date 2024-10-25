@extends('pages.anketas.base')

@section('title', 'Ошибка сервера')

@push('custom_styles')
    <style>
        .page-content {
            height: 100vh;
        }

        .status-icon {
            font-size: 70px;
        }

        .status-icon-wrong {
            color: #a70912;
        }

        .status-title {
            margin-top: 10px;
            font-size: 18px;
        }

        .status-text {
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
@endpush

@section('content')
    <main class="page-content d-flex align-items-stretch">
        <div class="container text-center">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12">
                    <div class="flex justify-content-center align-items-center">
                        <div>
                            <div>
                                <i class="fa fa-times-circle status-icon status-icon-wrong" aria-hidden="true"></i>
                            </div>
                            <div class="status-title">Ошибка сервера.</div>
                            <div class="status-text">Попробуйте обновить страницу или вернуться позже.</div>
                            <div class="status-text">{{ $message }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
