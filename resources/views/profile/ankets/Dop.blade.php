<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group row">
    <label class="col-md-3 form-control-label">ID водителя:</label>
    <article class="col-md-9">
        <input value="{{ $driver_id ?? '' }}" type="number" onchange="checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())" min="6" name="driver_id" class="MASK_ID_ELEM form-control">
        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">ID автомобиля:</label>
    <article class="col-md-9">
        <input value="{{ $car_id ?? '' }}" type="number" onchange="checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent())" min="6" name="car_id" class="MASK_ID_ELEM form-control">
        <p class="app-checker-prop"></p>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group row">
        <label class="col-md-3 form-control-label">Дата и время выдачи:</label>
        <article class="col-md-9">
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20" type="datetime-local" required value="{{ $default_current_date }}" name="anketa[0][date]" class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Тип осмотра:</label>
        <article class="col-md-9">
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

    <div class="anketa-bordered">

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Номер путевого листа:</label>
            <article class="col-md-9">
                <input value="{{ $number_list_road ?? '' }}" type="text" name="anketa[0][number_list_road]" class="form-control">
            </article>
        </div>

        {{--<div class="form-group row">
            <label class="col-md-3 form-control-label">Срок действия путевого листа:</label>
            <article class="col-md-9">
                <input value="{{ $date_number_list_road ?? '' }}" type="date" name="anketa[0][date_number_list_road]" class="form-control">
            </article>
        </div>--}}

    </div>

    <div class="anketa-delete"></div>
</div>
