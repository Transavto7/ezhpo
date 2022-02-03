<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group">
    <label class="form-control-label">ID водителя:</label>
    <article>
        <input value="{{ $driver_id ?? '' }}" type="number" onchange="checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())" min="6" name="driver_id" class="MASK_ID_ELEM form-control">
        <p class="app-checker-prop"></p>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group">
        <label class="form-control-label">Дата и время осмотра:</label>
        <article>
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20" type="datetime-local" value="{{ $default_current_date }}" name="anketa[0][date]" class="form-control">
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">ID автомобиля:</label>
        <article>
            <input value="{{ $car_id ?? '' }}" type="number" required onchange="checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent())" min="6" name="anketa[0][car_id]" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Тип осмотра:</label>
        <article>
            <select name="anketa[0][type_view]" required class="form-control">
                @isset($type_view)
                    <option disabled selected value="{{ $type_view }}">{{ $type_view }}</option>
                @endisset

                <option selected value="Предрейсовый">Предрейсовый</option>
                <option value="Послерейсовый">Послерейсовый</option>
                <option value="Предсменный">Предсменный</option>
                <option value="Послесменный">Послесменный</option>
            </select>
        </article>
    </div>

    <div class="form-group">
        <label class="form-control-label">Показания одометра:</label>
        <input type="text" value="{{ $odometer ?? '' }}" name="anketa[0][odometer]" class="form-control">
    </div>

    <div class="form-group">
        <label class="form-control-label">Номер путевого листа:</label>
        <input type="text" value="{{ $number_list_road ?? '' }}" name="anketa[0][number_list_road]" class="form-control">
    </div>

    {{--<div class="form-group">
        <label class="form-control-label">Срок действия путевого листа:</label>
        <input type="date" value="{{ $date_number_list_road ?? '' }}" name="anketa[0][date_number_list_road]" class="form-control">
    </div>--}}


    <div class="anketa-delete"></div>
</div>

<div class="form-group">
    <label class="form-control-label">Отметка о прохождении предрейсового контроля:</label>
    <select name="point_reys_control" required class="form-control">
        @isset($point_reys_control)
            <option disabled selected value="{{ $point_reys_control }}">{{ $point_reys_control }}</option>
        @endisset

        <option selected value="Пройден">Пройден</option>
        <option value="Не пройден">Не пройден</option>
    </select>
</div>
