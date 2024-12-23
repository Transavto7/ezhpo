@php
    /** @var $actions_policy \App\Models\Forms\ActionsPolicy\Contracts\PolicyInterface */
@endphp

<div class="form-group">
    <label class="form-control-label">ID водителя:</label>
    <article>
        <div class="d-flex">
            <input required
                   value="{{ $driver_id ?? '' }}"
                   @disabled($actions_policy->isAttributeDisabled('driver_id'))
                   type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent().parent(), {{ 'false' }})"
                   min="6"
                   name="driver_id"
                   class="MASK_ID_ELEM form-control">
            <a class="btn btn-outline-secondary camera-btn" data-field-type="driverId"><i class="fa fa-camera"></i></a>
        </div>
        <p class="app-checker-prop"></p>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group">
        <label class="form-control-label">Дата и время осмотра:</label>
        <article>
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20"
                   type="datetime-local"
                   @disabled($actions_policy->isAttributeDisabled('date'))
                   value="{{ $default_current_date ?? '' }}"
                   name="anketa[0][date]"
                   class="form-control inspection-date">
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">ID автомобиля:</label>
        <article>
            <div class="d-flex">
                <input required
                       value="{{ $car_id ?? '' }}"
                       @disabled($actions_policy->isAttributeDisabled('car_id'))
                       type="number"
                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent().parent(), {{ 'false' }})"
                       min="6"
                       name="anketa[0][car_id]"
                       class="MASK_ID_ELEM form-control car-input">
                <a class="btn btn-outline-secondary camera-btn" data-field-type="carId"><i class="fa fa-camera"></i></a>
            </div>
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Тип осмотра:</label>
        <article>
            <select name="anketa[0][type_view]"
                    @disabled($actions_policy->isAttributeDisabled('type_view'))
                    required
                    class="form-control type-view">
                <option value="Предрейсовый/Предсменный"
                        @if(strcasecmp($type_view ?? '', 'Предрейсовый/Предсменный') == 0) selected @endif>
                    Предрейсовый/Предсменный
                </option>
                <option value="Послерейсовый/Послесменный"
                        @if(strcasecmp($type_view ?? '', 'Послерейсовый/Послесменный') == 0) selected @endif>
                    Послерейсовый/Послесменный
                </option>
            </select>
            <p class="duplicate-indicator text-danger d-none" style="font-size: 0.7875rem">Осмотр с указанным водителем,
                датой и типом уже существует</p>
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
        <input type="text" value="{{ $odometer ?? '' }}"
               name="anketa[0][odometer]"
               class="form-control">
    </div>

    <div class="form-group">
        <label class="form-control-label">Номер путевого листа:</label>
        <input type="text" value="{{ $number_list_road ?? '' }}"
               name="anketa[0][number_list_road]"
               class="form-control">
    </div>

    <div class="anketa-delete"></div>
</div>

<div class="form-group">
    <label class="form-control-label">Отметка о прохождении предрейсового контроля:</label>
    <select name="point_reys_control" id="point_reys_control" required class="form-control">
        @isset($point_reys_control)
            <option disabled selected value="{{ $point_reys_control }}">{{ $point_reys_control }}</option>
        @endisset

        <option selected value="Пройден">Пройден</option>
        <option value="Не пройден">Не пройден</option>
    </select>
</div>

