<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group row">
    <label class="col-md-3 form-control-label">ID водителя:</label>
    <article class="col-md-9">
        <input value="{{ $driver_id ?? '' }}" type="number" onchange="checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())" required min="6" name="driver_id" class="MASK_ID_ELEM form-control">
        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group row">
        <label class="col-md-3 form-control-label">Дата и время осмотра:</label>
        <article class="col-md-9">
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20" type="datetime-local" required value="{{ $default_current_date }}" name="anketa[0][date]" class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Показания тонометра:</label>
        <article class="col-md-9">
            <input type="text" min="4" minlength="4" max="7" maxlength="7" placeholder="90/120 или 120/80 (пример)" name="anketa[0][tonometer]" value="{{ $tonometer ?? '' }}" class="form-control">
            <small>Недопустимо верхнее давление < 50 или > 220 , нижнее < 40 или > 160</small>
        </article>
    </div>

    <div class="anketa-bordered">


        <div class="form-group row">
            <label class="form-control-label col-md-3">Тип осмотра:</label>
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

        {{--        <div class="form-group row">--}}
{{--            <label class="col-md-3 form-control-label">Номер путевого листа:</label>--}}
{{--            <article class="col-md-9">--}}
{{--                <input value="{{ $number_list_road ?? '' }}" type="text" name="anketa[0][number_list_road]" class="form-control">--}}
{{--            </article>--}}
{{--        </div>--}}

        {{--<div class="form-group row">
            <label class="col-md-3 form-control-label">Срок действия путевого листа:</label>
            <article class="col-md-9">
                <input value="{{ $date_number_list_road ?? '' }}" type="date" name="anketa[0][date_number_list_road]" class="form-control">
            </article>
        </div>--}}

    </div>

    <div class="anketa-delete"></div>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Температура тела:</label>
    <article class="col-md-9">
        <input type="number" value="{{ $t_people ?? '' }}" name="t_people" class="form-control">
    </article>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Проба на алкоголь:</label>
    <article class="col-md-9">
        <select name="proba_alko" required class="form-control">
            @isset($proba_alko)
                <option disabled selected value="{{ $proba_alko }}">{{ $proba_alko }}</option>
            @endisset

            <option selected value="Отрицательно">Отрицательно</option>
            <option value="Положительно">Положительно</option>
        </select>
    </article>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Тест на наркотики:</label>
    <article class="col-md-9">
        <select name="test_narko" required class="form-control">
            @isset($test_narko)
                <option disabled selected value="{{ $test_narko }}">{{ $test_narko }}</option>
            @endisset

            <option selected value="Не проводился">Не проводился</option>
            <option value="Отрицательно">Отрицательно</option>
            <option value="Положительно">Положительно</option>
        </select>
    </article>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Мед показания:</label>
    <article class="col-md-9">
        <select name="med_view" required class="form-control">
            @isset($med_view)
                <option disabled selected value="{{ $med_view }}">{{ $med_view }}</option>
            @endisset

            <option selected value="В норме">В норме</option>
            <option value="Отстранение">Отстранение</option>
        </select>
    </article>
</div>
