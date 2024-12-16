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
                              action="{{ route('trip-tickets.store-medic-form', ['id' => $tripTicket->uuid]) }}"
                              class="form-horizontal"
                              onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                              enctype="multipart/form-data"
                              id="ANKETA_FORM">
                            @csrf

                            <input type="hidden" name="REFERER" value="{{ $previousUrl }}">

                            @include('profile.ankets.components.pvs', ['points' => App\Point::getAll()])

                            <div class="form-group d-flex">
                                <article>
                                    <a class="text-small" href="{{ route('trip-tickets.create-medic-form',
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

                                @if(! $shortForm)
                                    <div class="form-group">
                                        <label class="form-control-label">Тип осмотра:</label>
                                        <article>
                                            <select name="anketa[0][type_view]" required class="form-control type-view">
                                                <option value="Предрейсовый/Предсменный" selected>
                                                    Предрейсовый/Предсменный
                                                </option>
                                                <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный
                                                </option>
                                            </select>
                                            <p class="duplicate-indicator text-danger d-none"
                                               style="font-size: 0.7875rem">Осмотр с
                                                указанным водителем, датой и типом уже существует</p>
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Температура тела:</label>
                                        <article>
                                            <input type="number"
                                                   step="0.1"
                                                   min="30"
                                                   max="46"
                                                   name="t_people"
                                                   class="form-control">
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Проба на алкоголь:</label>
                                        <article>
                                            <select name="proba_alko"
                                                    class="form-control"
                                                    required
                                                    onchange="updateAlcometerResult()">
                                                <option selected value="Отрицательно">Отрицательно</option>
                                                <option value="Положительно">Положительно</option>
                                            </select>
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Уровень алкоголя в выдыхаемом воздухе:</label>
                                        <article>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   name="alcometer_result"
                                                   class="form-control"
                                                   onchange="updateProbaAlko()">
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Тест на наркотики:</label>
                                        <article>
                                            <select name="test_narko" required class="form-control">
                                                <option selected value="Не проводился">Не проводился</option>
                                                <option value="Отрицательно">Отрицательно</option>
                                                <option value="Положительно">Положительно</option>
                                            </select>
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Мед показания:</label>
                                        <article>
                                            <select name="med_view" id="med_view" required class="form-control">
                                                <option value="В норме">В норме</option>
                                                <option value="Отстранение">Отстранение</option>
                                            </select>
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Показания тонометра:</label>
                                        <article>
                                            <input type="text"
                                                   min="4"
                                                   minlength="4"
                                                   max="7"
                                                   maxlength="7"
                                                   placeholder="90/120 или 120/80 (пример)"
                                                   name="anketa[0][tonometer]"
                                                   class="form-control">
                                            <small></small>
                                        </article>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-control-label">Пульс:</label>
                                        <article>
                                            <input type="number"
                                                   maxlength="7"
                                                   name="anketa[0][pulse]"
                                                   class="form-control">
                                            <small></small>
                                        </article>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label class="form-control-label">Период выдачи ПЛ:</label>
                                        <article>
                                            <input type="month"
                                                   oninput="changeFormRequire(this, 'pl-date')"
                                                   name="anketa[0][period_pl]"
                                                   class="form-control pl-period">
                                        </article>
                                    </div>

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
                                @endif
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
