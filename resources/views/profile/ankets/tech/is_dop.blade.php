<div class="form-group">
    <label class="form-control-label">ID компании:</label>
    <article>
        <input value="{{ $company_id ?? '' }}"
               required
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
        <div class="d-flex">
            <input value="{{ $driver_id ?? '' }}"
                   type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent().parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
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
        <label class="form-control-label">ID автомобиля:</label>
        <article>
            <div class="d-flex">
                <input value="{{ $car_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent().parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                       min="6"
                       name="anketa[0][car_id]"
                       class="MASK_ID_ELEM form-control car-input">
                <a class="btn btn-outline-secondary camera-btn" data-field-type="driverId"><i class="fa fa-camera"></i></a>
            </div>
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Дата осмотра ПЛ:</label>
        <article>
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20"
                   type="datetime-local"
                   @if (!isset($period_pl)) required @endif
                   oninput="changeFormRequire(this, 'pl-period')"
                   @isset ($date) value="{{ $default_current_date ?? '' }}" @endisset
                   name="anketa[0][date]"
                   class="form-control pl-date">
        </article>
    </div>

    @if(empty($id))
        <div class="form-group">
            <label class="form-control-label">Доп. даты:</label>
            <article>
                <input type="date"
                       name="anketa[0][dates]"
                       class="form-control date-range">
            </article>
        </div>
    @endif

    <div class="form-group">
        <label class="form-control-label">Период выдачи ПЛ:</label>
        <article>
            <input type="month"
                   @if (!isset($date)) required @endif
                   oninput="changeFormRequire(this, 'pl-date')"
                   value="{{ isset($period_pl) ? $period_pl : '' }}"
                   name="anketa[0][period_pl]"
                   class="form-control pl-period">
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Номер путевого листа:</label>
        <article>
            <input type="text"
                   value="{{ $number_list_road ?? '' }}"
                   name="anketa[0][number_list_road]"
                   class="form-control">
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Тип осмотра:</label>
        <article>
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
        <article>
            <input type="text"
                   value="{{ $odometer ?? '' }}"
                   name="anketa[0][odometer]"
                   class="form-control">
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>
