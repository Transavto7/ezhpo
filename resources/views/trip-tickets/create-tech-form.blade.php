@extends('layouts.app')

@section('title', $title)
@section('sidebar', 1)

@php
    use Carbon\Carbon;
    $shortForm = $shortForm ?? request()->get('shortForm', '0') === '1';
    $previousUrl = request()->get('previousUrl', url()->previous());
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
                        @foreach($errors ?? [] as $error)
                            <div class="alert alert-danger" role="alert">{{ $error }}</div>
                        @endforeach

                        <form method="POST"
                              action="{{ route('trip-tickets.store-tech-form', ['id' => $tripTicket->uuid]) }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data"
                              id="ANKETA_FORM">
                            @csrf

                            <input type="hidden" name="REFERER" value="{{ $previousUrl }}">

                            @include('profile.ankets.components.pvs', ['points' => App\Point::getAll()])

                            <div class="form-group d-flex">
                                <article>
                                    <a class="text-small" href="{{ route('trip-tickets.create-tech-form',
                                        ['id' => $tripTicket->uuid, 'shortForm' => !$shortForm, 'previousUrl' => $previousUrl]) }}">
                                        <input onchange="this.parentNode.click()" type="checkbox"
                                               @if($shortForm) checked @endif id="change-form-type">
                                    </a>
                                    <label class="form-control-label ml-1 mb-0" for="change-form-type">Создать неполный
                                        осмотр</label>
                                    <input type="hidden" name="is_dop" value="{{ $shortForm ?? 0 }}">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID компании:</label>
                                <article>
                                    <input required
                                           disabled
                                           value="{{ $tripTicket->company_id }}"
                                           type="number"
                                           oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                                           min="5"
                                           name="company_id"
                                           class="MASK_ID_ELEM form-control">
                                    <p class="app-checker-prop"></p>
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">ID водителя:</label>
                                <article>
                                    <input type="number"
                                           disabled
                                           @if(! $shortForm)required @endif
                                           value="{{ $tripTicket->driver_id }}"
                                           oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ 'false' }})"
                                           min="6"
                                           name="driver_id"
                                           class="MASK_ID_ELEM form-control">
                                    <div class="app-checker-prop"></div>
                                </article>
                            </div>

                            <div class="cloning" id="cloning-first">
                                <div class="form-group">
                                    <label class="form-control-label">ID автомобиля:</label>
                                    <article>
                                        <div class="d-flex">
                                            <input type="number"
                                                   disabled
                                                   @if(! $shortForm)required @endif
                                                   value="{{ $tripTicket->car_id }}"
                                                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent().parent(), {{ 'false' }})"
                                                   min="6"
                                                   name="anketa[0][car_id]"
                                                   class="MASK_ID_ELEM form-control car-input">
                                        </div>
                                        <p class="app-checker-prop"></p>
                                    </article>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Дата и время осмотра:</label>
                                    <article>
                                        <input oninput="changeFormRequire(this, 'pl-period')"
                                               required
                                               value="{{ $tripTicket->start_date ? $tripTicket->start_date.'T00:00' : null }}"
                                               min="{{ $tripTicket->start_date ?: '1970-01-01' }}T00:00"
                                               max="{{ $tripTicket->start_date ?: '2100-01-01' }}T23:59"
                                               type="datetime-local"
                                               name="anketa[0][date]"
                                               class="form-control pl-date inspection-date">
                                    </article>
                                </div>

                                @if($shortForm)
                                    <div class="form-group">
                                        <label class="form-control-label">Период выдачи ПЛ:</label>
                                        <article>
                                            <input type="month"
                                                   oninput="changeFormRequire(this, 'pl-date')"
                                                   name="anketa[0][period_pl]"
                                                   class="form-control pl-period">
                                        </article>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-control-label">Тип осмотра:</label>
                                    <article>
                                        <select name="anketa[0][type_view]" class="form-control type-view">
                                            <option value="Предрейсовый/Предсменный" selected>
                                                Предрейсовый/Предсменный
                                            </option>
                                            <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный
                                            </option>
                                        </select>
                                        <p class="duplicate-indicator text-danger d-none"
                                           style="font-size: 0.7875rem">Осмотр с указанным водителем, датой и типом
                                            уже существует</p>
                                    </article>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Предыдущие показания одометра:</label>
                                    <div class="input-group mb-3">
                                        <input type="text"
                                               readonly
                                               disabled
                                               name="anketa[0][previous_odometer]"
                                               class="form-control h-auto">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-success"
                                                    type="button"
                                                    onclick="loadPreviousOdometer()">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Показания одометра:</label>
                                    <input type="text"
                                           name="anketa[0][odometer]"
                                           class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Номер путевого листа:</label>
                                    <input type="text"
                                           name="anketa[0][number_list_road]"
                                           class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Отметка о прохождении предрейсового
                                        контроля:</label>
                                    <select name="point_reys_control" id="point_reys_control" required
                                            class="form-control">
                                        <option selected value="Пройден">Пройден</option>
                                        <option value="Не пройден">Не пройден</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <a href="{{ $previousUrl }}" class="m-center btn btn-sm btn-info">Вернуться в реестр</a>
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
