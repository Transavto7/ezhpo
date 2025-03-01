@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)
@section('class-page', 'page-anketa anketa-' . $type_anketa)

@php
    $is_dop = $is_dop ?? request()->get('is_dop', '0') === '1';
    $created = \Illuminate\Support\Facades\Session::get('created', []);
    $redDates = \Illuminate\Support\Facades\Session::get('redDates', []);
@endphp

@section('custom-scripts')
    <script type="text/javascript">
        if (screen.width <= 700) {
            ANKETA_FORM_VIEW.insertBefore(ANKETA_FORM_ROOT, ANKETA_FORM_VIEW_FIRST)
        }

        let notAdmittedReasons = @json($not_admitted_reasons ?? []);
        notAdmittedReasons.filter((reason) => ['tonometer', 'proba_alko', 't_people'].includes(reason))

        notAdmittedReasons.forEach((notAdmittedReason) => {
            let input

            switch (notAdmittedReason) {
                case 'tonometer':
                    input = $('input[name="anketa[0][tonometer]"]')
                    break;
                case 'proba_alko':
                    input = $('select[name="proba_alko"]')
                    break;
                case 't_people':
                    input = $('input[name="t_people"]')
                    break;
            }

            if (input) {
                input.css("background", "pink")
            }
        })

        let needApproveAdmitting = notAdmittedReasons.length > 0

        function approveAdmitting() {
            const admitted = $("input[name='admitted']:checked");

            if (!needApproveAdmitting || !admitted || (admitted.val() !== 'Допущен')) {
                $('#ANKETA_FORM').trigger('submit')

                return
            }

            window.swal.fire({
                title: 'Отклонение от параметров!',
                text: 'Обратите внимание, у водителя имеются отклонения от установленных предельных параметров. Подтвердите действие.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Допустить водителя',
                cancelButtonText: "Отмена",
            }).then(function (result) {
                if (result.isConfirmed) {
                    $('#ANKETA_FORM').trigger('submit')
                } else {
                    admitted.removeAttr('checked')
                }
            })
        }

        function updateAlcometerResult() {
            const input = $('input[name="alcometer_result"]')[0]

            if (input === undefined) {
                return
            }

            const select = $('select[name="proba_alko"] option:selected')[0]

            if (select === undefined) {
                return
            }

            if (select.value === 'Отрицательно') {
                input.value = 0
            }
        }

        function updateProbaAlko() {
            const input = $('input[name="alcometer_result"]')[0]

            if (input === undefined) {
                return
            }

            const select = $('select[name="proba_alko"]')[0]

            if (select === undefined) {
                return
            }

            if (input.value > 0) {
                select.value = 'Положительно'
            } else {
                select.value = 'Отрицательно'
            }
        }
    </script>
@endsection

@section('content')
    @include('profile.ankets.components.fast-scroll')

    <div class="row" data-anketa="{{ $anketa_view }}" id="ANKETA_FORM_VIEW">
        <!-- Анкета -->
        <div class="col-lg-3" id="ANKETA_FORM_VIEW_FIRST">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка автомобиля</b></p>

                    <div id="CARD_CAR">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3" id="ANKETA_FORM_ROOT">
            <div class="card">
                <div class="card-body">
                    <p><b>{{ $title }}</b></p>

                    <!-- Анкета: {{ $title }} -->
                    <article class="anketa anketa-fields">
                        @foreach($errors ?? [] as $error)
                            <div class="alert alert-danger" role="alert">{{ $error }}</div>
                        @endforeach

                        @if(count($created ?? []))
                            <div class="row">
                                @foreach($created ?? [] as $form)
                                    <div class="col-md-12">
                                        <div class="card p-2 text-xsmall">
                                            <b>"{{ $title }}" успешно создан!</b>
                                            <br/> ID осмотра: {{ $form->id }}

                                            @if($form->driver && $form->driver->fio)
                                                <br/>
                                                <b>Водитель: {{ $form->driver->fio }}</b>
                                            @endif

                                            @if($form->details->type_view)
                                                Тип осмотра:<b>{{ $form->details->type_view }}</b>
                                            @endif

                                            @if($form->details->car && $form->details->car->gos_number)
                                                <br/>
                                                <b>Госномер автомобиля: {{ $form->details->car->gos_number }}</b>
                                            @endif

                                            @if($form->date)
                                                <div>
                                                    <i>Дата проведения осмотра:
                                                        <br/><b>{{ $form->date }}</b></i>
                                                </div>
                                            @elseif($form->details->period_pl)
                                                <div>
                                                    <i>Период проведения
                                                        осмотра:<br/><b>{{ $form->details->period_pl }}</b></i>
                                                </div>
                                            @endif

                                            @if(($form->details->admitted === 'Не допущен') && user()->access('medic_closing_edit'))
                                                <a class="btn primary btn-sm btn-table"
                                                   href="{{ route('docs.get', ['type' => 'closing', 'anketa_id' => $form->id]) }}">
                                                    Мед. заключение
                                                </a>
                                            @endif

                                            @foreach($redDates ?? [] as $redDateKey => $redDateVal)
                                                <p class="text-danger">
                                                    {{ __('ankets.'.$redDateKey) }}
                                                    : {{ $redDateVal['value'] }}
                                                </p>
                                            @endforeach

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="alert alert-success">
                                <b>{{ \Illuminate\Support\Facades\Session::get('message') }}</b>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ isset($anketa_route) ? route($anketa_route, $id) : route('forms.store') }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data"
                              id="ANKETA_FORM">

                            @csrf

                            @if(isset($anketa_route) && $id)
                                <input type="hidden" name="REFERER" value="{{ url()->previous() }}">
                            @endif

                            @include($anketa_view)

                            @if(!isset($anketa_route))
                                <div id="cloning-append"></div>

                                <button type="button" id="ANKETA_CLONE_TRIGGER" class="anketa__addnew">
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif

                            <div class="form-group row">
                                @hasSection('ankets_submit')
                                    @yield('ankets_submit')
                                @else
                                    @if(isset($anketa_route))
                                        <a href="{{ url()->previous() }}"
                                           class="m-center btn btn-info">
                                            {{ __('Вернуться в журнал') }}
                                        </a>
                                    @endif
                                    <button type="submit"
                                            class="m-center btn btn-sm btn-success submit-btn">
                                        {{ __('ankets.submit') }}
                                    </button>
                                @endif
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка компании</b></p>

                    <div id="CARD_COMPANY">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <p><b>Карточка водителя</b></p>

                    <div id="CARD_DRIVER">
                        Не найдено
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
