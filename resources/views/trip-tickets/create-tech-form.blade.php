@extends('trip-tickets.create-form')

@php
    use Carbon\Carbon;
    use App\Enums\FormTypeEnum;
@endphp

@section('form')
    <form method="POST"
          action="{{ route('trip-tickets.store-form', ['id' => $tripTicket->uuid]) }}"
          class="form-horizontal"
          onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
          enctype="multipart/form-data"
          id="ANKETA_FORM">
        @csrf

        <input type="hidden" name="type_anketa" value="{{ FormTypeEnum::TECH }}">
        <input type="hidden" name="anketa[0][number_list_road]" value="{{ $tripTicket->ticket_number }}">

        @include('profile.ankets.components.pvs', ['points' => App\Point::getAll()])

        <div class="form-group">
            <label class="form-control-label">ID компании:</label>
            <article>
                <input required
                       readonly
                       value="{{ $tripTicket->company_id }}"
                       type="number"
                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                       class="MASK_ID_ELEM form-control">
                <p class="app-checker-prop"></p>
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">ID водителя:</label>
            <article>
                <input type="number"
                       @if($tripTicket->driver_id) readonly @endif
                       required
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
                               @if($tripTicket->car_id) readonly @endif
                               required
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
                           @if($tripTicket->period_pl && $tripTicket->start_date === null)
                               min="{{ Carbon::parse($tripTicket->period_pl)->startOfMonth()->format('Y-m-d\TH:i') }}"
                           max="{{ Carbon::parse($tripTicket->period_pl)->endOfMonth()->endOfDay()->format('Y-m-d\TH:i') }}"
                           @else
                               min="{{ $tripTicket->start_date ?: '1970-01-01' }}T00:00"
                           max="{{ $tripTicket->start_date ?: '2100-01-01' }}T23:59"
                           @endif
                           type="datetime-local"
                           name="anketa[0][date]"
                           class="form-control pl-date inspection-date">
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

            <div class="form-group">
                <label class="form-control-label">Предыдущие показания одометра:</label>
                <div class="input-group mb-3">
                    <input type="text"
                           readonly
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
            <a href="{{ route('trip-tickets.index') }}" class="m-center btn btn-sm btn-info">Вернуться в реестр</a>
            <button type="submit" class="m-center btn btn-sm btn-success submit-btn">Сохранить
            </button>
        </div>
    </form>
@endsection
