<input type="hidden" name="type_anketa" value="{{ $type_anketa }}"/>

@include('profile.ankets.components.pvs')

@include('profile.ankets.components.is_dop')

@if($is_dop)
    <div class="form-group row">
        <label class="form-control-label col-md-3">ID компании:</label>
        <article class="col-md-9">
            <input value="{{ $company_id ?? '' }}" required type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   min="5" name="company_id" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="form-group row">
        <label class="form-control-label col-md-3">ID водителя:</label>
        <article class="col-md-9">
            <input value="{{ $driver_id ?? '' }}" type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   min="6" name="driver_id" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="cloning" id="cloning-first">

        <div class="form-group row">
            <label class="col-md-3 form-control-label">ID автомобиля:</label>
            <article class="col-md-9">
                <input value="{{ $car_id ?? '' }}" type="number"
                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                       min="6" name="anketa[0][car_id]" class="MASK_ID_ELEM form-control car-input">
                <p class="app-checker-prop"></p>
            </article>
        </div>

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Дата осмотра ПЛ:</label>
            <article class="col-md-9">
                <input min="1900-02-20T20:20"
                       max="2999-02-20T20:20" type="datetime-local"
                       @if (!isset($period_pl)) required @endif
                       oninput="changeFormRequire(this, 'pl-period')"
                       @isset ($date) value="{{ $default_current_date ?? '' }}" @endisset
                       name="anketa[0][date]" class="form-control pl-date">
            </article>
        </div>

        @if(empty($id))
            <div class="form-group row">
                <label class="col-md-3 form-control-label">Доп. даты:</label>
                <article class="col-md-9">
                    <input type="date"
                           name="anketa[0][dates]" class="form-control date-range">
                </article>
            </div>
        @endif

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Период выдачи ПЛ:</label>
            <article class="col-md-9">
                <input type="month"
                       @if (!isset($date)) required @endif
                       oninput="changeFormRequire(this, 'pl-date')"
                       value="{{ isset($period_pl) ? $period_pl : '' }}"
                       name="anketa[0][period_pl]"
                       class="form-control pl-period">
            </article>
        </div>

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Номер путевого листа:</label>
            <article class="col-md-9">
                <input type="text" value="{{ $number_list_road ?? '' }}" name="anketa[0][number_list_road]"
                       class="form-control">
            </article>
        </div>

        <div class="form-group row">
            <label class="form-control-label col-md-3">Тип осмотра:</label>
            <article class="col-md-9">
                <select name="anketa[0][type_view]" class="form-control">
                    <option value="Предрейсовый/Предсменный"
                            @if(strcasecmp($type_view ?? '', 'Предрейсовый/Предсменный') == 0) selected @endif>
                        Предрейсовый/Предсменный
                    </option>
                    <option value="Послерейсовый/Послесменный"
                            @if(strcasecmp($type_view ?? '', 'Послерейсовый/Послесменный') == 0) selected @endif>
                        Послерейсовый/Послесменный
                    </option>
                </select>
            </article>
        </div>

        <div class="form-group row">
            <label class="form-control-label col-md-3">Предыдущие показания одометра:</label>
            <div class="input-group mb-3 col-md-9">
                <input type="text" readonly disabled name="anketa[0][previous_odometer]" class="form-control h-auto">
                <div class="input-group-append">
                    <button class="btn btn-outline-success" type="button" onclick="loadPreviousOdometer()">Получить</button>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Показания одометра:</label>
            <article class="col-md-9">
                <input type="text" value="{{ $odometer ?? '' }}" name="anketa[0][odometer]" class="form-control">
            </article>
        </div>

        <div class="anketa-delete"></div>
    </div>
@else
    <div class="form-group">
        <label class="form-control-label">ID водителя:</label>
        <article>
            <input required value="{{ $driver_id ?? '' }}" type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   min="6" name="driver_id" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="cloning" id="cloning-first">
        <div class="form-group">
            <label class="form-control-label">Дата и время осмотра:</label>
            <article>
                <input min="1900-02-20T20:20"
                       max="2999-02-20T20:20" type="datetime-local" value="{{ $default_current_date ?? '' }}"
                       name="anketa[0][date]" class="form-control">
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">ID автомобиля:</label>
            <article>
                <input required value="{{ $car_id ?? '' }}" type="number"
                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                       min="6" name="anketa[0][car_id]" class="MASK_ID_ELEM form-control car-input">
                <p class="app-checker-prop"></p>
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">Тип осмотра:</label>
            <article>
                <select name="anketa[0][type_view]" required class="form-control">
                    <option value="Предрейсовый/Предсменный"
                            @if(strcasecmp($type_view ?? '', 'Предрейсовый/Предсменный') == 0) selected @endif>
                        Предрейсовый/Предсменный
                    </option>
                    <option value="Послерейсовый/Послесменный"
                            @if(strcasecmp($type_view ?? '', 'Послерейсовый/Послесменный') == 0) selected @endif>
                        Послерейсовый/Послесменный
                    </option>
                </select>
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">Предыдущие показания одометра:</label>
            <div class="input-group mb-3">
                <input type="text" readonly disabled name="anketa[0][previous_odometer]" class="form-control h-auto">
                <div class="input-group-append">
                    <button class="btn btn-outline-success" type="button" onclick="loadPreviousOdometer()">Получить</button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-control-label">Показания одометра:</label>
            <input type="text" value="{{ $odometer ?? '' }}" name="anketa[0][odometer]" class="form-control">
        </div>

        <div class="form-group">
            <label class="form-control-label">Номер путевого листа:</label>
            <input type="text" value="{{ $number_list_road ?? '' }}" name="anketa[0][number_list_road]"
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
@endif

