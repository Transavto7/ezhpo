@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)
@section('class-page', 'page-anketa anketa-' . $type_anketa)

@section('content')

<div class="row" data-anketa="{{ $anketa_view }}">
    <!-- Анкета -->
    <div class="col-lg-2">
        <div class="card">
            <div class="card-body">
                <p><b>Карточка компании</b></p>

                <div id="CARD_COMPANY">
                    Не найдено
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h3 class="text-center">{{ $title }}</h3>
                <hr>

                <!-- Анкета: {{ $title }} -->
                <article class="anketa anketa-fields">
                    @isset($_GET['errors'])
                        @foreach($_GET['errors'] as $error)
                            <div class="alert alert-danger" role="alert">{{ $error }}</div>
                        @endforeach
                    @endisset

                    @isset($_GET['createdId'])
                        <div class="row">
                            @foreach($_GET['createdId'] as $cId)
                                @php $anketa = \App\Anketa::find($cId) @endphp


                                @isset($anketa)
                                    <div class="col-md-6">
                                        <div class="card p-2 text-xsmall">

                                            @if($type_anketa === 'medic')
                                                <b>"{{ $title }}" успешно создан!</b>
                                                <br/> ID осмотра: {{ $cId }}

                                                @isset($anketa->driver_id)
                                                    @isset(\App\Driver::where('hash_id', $anketa->driver_id)->first()->fio)
                                                        <br /> <b>Водитель: {{ \App\Driver::where('hash_id', $anketa->driver_id)->first()->fio }}</b>
                                                    @endisset
                                                @endisset
                                                <div>
                                                    <i>Дата проведения осмотра: <br/><b>{{ $anketa->date }}</b></i>
                                                </div>
                                            @else
                                                <b>"{{ $title }}" (ID: {{ $cId }}) успешно создан!</b>

                                                @isset($anketa->driver_id)
                                                    @isset(\App\Driver::where('hash_id', $anketa->driver_id)->first()->fio)
                                                        <br /> <b>Водитель: {{ \App\Driver::where('hash_id', $anketa->driver_id)->first()->fio }}</b>
                                                    @endisset
                                                @endisset

                                                @isset($anketa->car_id)
                                                    @isset(\App\Driver::where('hash_id', $anketa->car_id)->first()->gos_number)
                                                        <br /> <b>Госномер автомобиля: {{ \App\Driver::where('hash_id', $anketa->car_id)->first()->gos_number }}</b>
                                                    @endisset
                                                @endisset

                                                <div>
                                                    Дата проведения осмотра <b>{{ $anketa->date }}</b>
                                                </div>
                                            @endif

                                            @isset($_GET['redDates'])
                                                @if(is_array($_GET['redDates']))
                                                    @if(count($_GET['redDates']) > 0)
                                                        @foreach($_GET['redDates'] as $redDateKey => $redDateVal)
                                                            <p class="text-danger">
                                                                {{ __('ankets.'.$redDateKey) }}: {{ $redDateVal['value'] }}
                                                            </p>
                                                                {{--<input type="date" id="field_{{ $redDateKey }}" name="{{ $redDateKey }}" value="{{ $redDateVal['value'] }}">--}}
                                                               {{-- <a href="{{ route('updateDDate', [
                                                                'item_model' => $redDateVal['item_model'],
                                                                'item_id' => $redDateVal['item_id'],
                                                                'item_field' => $redDateVal['item_field']
                                                            ]) }}" data-field="#field_{{ $redDateKey }}" class="JS_CHANGE_FIELD_MODEL text-success text-bold"><i class="fa fa-save"></i></a></p>--}}
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endisset

                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endisset

                    @isset($_GET['msg'])
                        <div class="alert alert-success">
                            <b>{{ $_GET['msg'] }}</b>
                        </div>
                    @endif

                    <form method="POST"
                          @if(isset($anketa_route))
                            action="{{ route($anketa_route, $id) }}"
                          @else
                            action="{{ route('addAnket') }}"
                          @endif
                          class="form-horizontal"

                          enctype="multipart/form-data"
                    >
                        @csrf

                        @include($anketa_view)

                        @if(!isset($anketa_route))
                            <div id="cloning-append"></div>

                            <button type="button" id="ANKETA_CLONE_TRIGGER" class="anketa__addnew">
                                <i class="fa fa-plus"></i>
                            </button>
                        @endif

                        <div class="form-group row">
                            <button type="submit" class="m-center btn btn-success">{{ __('ankets.submit') }}</button>
                        </div>
                    </form>
                </article>

            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="card">
            <div class="card-body">
                <p><b>Карточка водителя</b></p>

                <div id="CARD_DRIVER">
                    Не найдено
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-2">
        <div class="card">
            <div class="card-body">
                <p><b>Карточка автомобиля</b></p>

                <div id="CARD_CAR">
                    Не найдено
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
