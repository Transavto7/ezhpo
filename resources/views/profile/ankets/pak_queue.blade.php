@include('profile.ankets.components.pvs')

<input type="hidden" name="type_anketa" value="medic">
<input type="hidden" name="flag_pak" value="СДПО Р">

<div class="form-group row">
    <label class="col-md-3 form-control-label"><b>Решение</b></label>
    <article class="col-md-9">

        <label class="btn btn-success btn-sm">
            <i class="fa fa-check-circle"></i> Принять
            <input type="radio" value="Допущен" name="admitted" />
        </label>

        &nbsp;&nbsp;&nbsp;&nbsp;

        <label class="btn btn-danger btn-sm">
            <i class="fa fa-close"></i>
            Отклонить
            <input type="radio" value="Недопущен" name="admitted" />
        </label>

    </article>
</div>

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

    <div class="anketa-delete"></div>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Температура тела:</label>
    <article class="col-md-9">
        <input type="number" value="{{ $t_people ?? '' }}" name="t_people" class="form-control">
    </article>
</div>

