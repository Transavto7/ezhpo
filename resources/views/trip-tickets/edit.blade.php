@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@php
    use Carbon\Carbon;
    $created = \Illuminate\Support\Facades\Session::get('created', []);
    /** @var App\Models\TripTicket $tripTicket */
@endphp

@section('custom-scripts')
    <script type="text/javascript">
        if (screen.width <= 700) {
            ANKETA_FORM_VIEW.insertBefore(ANKETA_FORM_ROOT, ANKETA_FORM_VIEW_FIRST)
        }

        $(document).ready(function () {
            const datesSelector = $("input[name='additional_dates']")
            initDatePicker(datesSelector)
        })
    </script>
@endsection

@section('content')
    @include('profile.ankets.components.fast-scroll')

    <div class="row" id="ANKETA_FORM_VIEW">
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

                    <article class="anketa anketa-fields">
                        @if(\Illuminate\Support\Facades\Session::has('message'))
                            <div class="alert alert-success">
                                <b>{{ \Illuminate\Support\Facades\Session::get('message') }}</b>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('trip-tickets.update', ['id' => $tripTicket->uuid]) }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="REFERER" value="{{ url()->previous() }}">

                            <div class="form-group">
                                <label class="form-control-label">ID компании:</label>
                                <article>
                                    <input value="{{ $tripTicket->company_id }}"
                                        type="number"
                                        oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                                        min="5"
                                        name="company_id"
                                        class="MASK_ID_ELEM form-control"
                                        disabled>
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID водителя:</label>
                                <article>
                                    <div class="d-flex">
                                        <input type="number"
                                            @if($tripTicket->driver_id !== null)
                                                value="{{ $tripTicket->driver_id }}"
                                                disabled
                                            @endif
                                            oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent().parent(), {{ 'false' }})"
                                            min="6"
                                            name="driver_id"
                                            class="MASK_ID_ELEM form-control">
                                    </div>
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID автомобиля:</label>
                                <article>
                                    <div class="d-flex">
                                        <input type="number"
                                               @if($tripTicket->car_id !== null)
                                                   value="{{ $tripTicket->car_id }}"
                                                   disabled
                                               @endif
                                               oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent().parent(), {{ 'false' }})"
                                               min="6"
                                               name="car_id"
                                               class="MASK_ID_ELEM form-control car-input">
                                    </div>
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Дата начала действия:</label>
                                <article>
                                    <input type="date"
                                           @if($tripTicket->period_pl)
                                               min="{{ Carbon::parse($tripTicket->period_pl)->startOfMonth()->format('Y-m-d') }}"
                                               max="{{ Carbon::parse($tripTicket->period_pl)->endOfMonth()->format('Y-m-d') }}"
                                           @else
                                               min="1970-01-01"
                                               max="2100-01-01"
                                           @endif
                                           value="{{ $tripTicket->start_date }}"
                                           name="start_date"
                                           class="form-control">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Период выдачи ПЛ:</label>
                                <article>
                                    <input type="month"
                                           value="{{ $tripTicket->period_pl }}"
                                           disabled
                                           name="period_pl"
                                           class="form-control period_pl">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Срок действия, дней:</label>
                                <input type="number" value="{{ $tripTicket->validity_period }}" required
                                       name="validity_period" class="form-control" min="1">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Номер путевого листа:</label>
                                <input type="text" value="{{ $tripTicket->ticket_number }}"
                                       name="ticket_number" disabled
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Вид сообщения:</label>
                                <article>
                                    <select name="logistics_method" required class="form-control type-view">
                                        @foreach(\App\Enums\LogisticsMethodEnum::labels() as $key => $label)
                                            <option value="{{ $key }}"
                                            @if($tripTicket->logistics_method === $key)
                                                {{'selected'}}
                                                @endif
                                            >{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Вид перевозки:</label>
                                <article>
                                    <select name="transportation_type" required class="form-control type-view">
                                        @foreach(\App\Enums\TransportationTypeEnum::labels() as $key => $label)
                                            <option value="{{ $key }}"
                                            @if($tripTicket->transportation_type === $key)
                                                {{'selected'}}
                                                @endif
                                            >{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Код бумажного шаблона:</label>
                                <article>
                                    <select name="template_code" required class="form-control type-view">
                                        @foreach(\App\Enums\TripTicketTemplateEnum::labels() as $key => $label)
                                            <option value="{{ $key }}"
                                            @if($tripTicket->template_code === $key)
                                                {{'selected'}}
                                                @endif
                                            >{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </article>
                            </div>

                            <div class="form-group row mb-0">
                                <a href="{{ url()->previous() }}" class="m-center btn btn-sm btn-info">Вернуться в
                                    реестр</a>
                                <button type="submit" class="m-center btn btn-sm btn-success submit-btn">Сохранить
                                </button>
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
