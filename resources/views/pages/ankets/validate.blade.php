@extends('layouts.app')

@if($verified)
    @section('title', 'Осмотр верифицирован')
@else
    @section('title', 'Осмотр не верифицирован')
@endif
@section('title', 'Верификация осмотра')

@section('custom-styles')
    <style>
        html, body, #app, .page-content {
            height: 100%;
        }

        .content-inner {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .status-icon {
            font-size: 70px;
        }
        .status-icon-success {
            color: #2fa360;
        }
        .status-icon-wrong {
            color: #a70912;
        }
        .status-title {
            margin-top: 10px;
            font-size: 18px;
        }

        .verified-item {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
        }

        .verified-item + .verified-item {
            margin-top: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="container text-center">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-12">
                <div class="flex justify-content-center align-items-center">
                    <div>
                        @if($verified)
                            <div>
                                <i class="fa fa-check-circle status-icon status-icon-success" aria-hidden="true"></i>
                            </div>
                            <div class="status-title">Осмотр верифицирован</div>

                            <div class="mt-4 d-flex flex-column align-items-center">
                                @if($anketInfo['number'])
                                    <div class="verified-item">
                                        <b>Номер осмотра:</b>
                                        <span>{{ $anketInfo['number'] }}</span>
                                    </div>
                                @endif

                                @if($anketInfo['date'])
                                    <div class="verified-item">
                                        <b>Наименование компании:</b>
                                        <span>{{ $anketInfo['companyName'] }}</span>
                                    </div>
                                @endif

                                @if($anketInfo['date'])
                                    <div class="verified-item">
                                        <b>Дата осмотра:</b>
                                        <span>{{ $anketInfo['date'] }}</span>
                                    </div>
                                @endif

                                @if($anketInfo['driverName'])
                                    <div class="verified-item">
                                        <b>ФИО водителя:</b>
                                        <span>{{ $anketInfo['driverName'] }}</span>
                                    </div>
                                @endif

                                @if($anketInfo['carGosNumber'])
                                    <div class="verified-item">
                                        <b>Гос. номер автомобиля:</b>
                                        <span>{{ $anketInfo['carGosNumber'] }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div>
                                <i class="fa fa-times-circle status-icon status-icon-wrong" aria-hidden="true"></i>
                            </div>
                            <div class="status-title">Осмотр не верифицирован</div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
