<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group row">
    <label class="col-md-3 form-control-label">Название компании:</label>
    <article class="col-md-9">
        @include('templates.elements_field', [
            'v' => $company_fields,
            'k' => 'company_name',
            'is_required' => '',
            'model' => 'Company',
            'default_value' => isset($company_name) ? $company_name : request()->get('company_name')
        ])

        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="form-group row">
    <label class="form-control-label col-md-3">ID водителя:</label>
    <article class="col-md-9">
        <input value="{{ $driver_id ?? '' }}" type="number" oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())" min="6" name="driver_id" class="MASK_ID_ELEM form-control">
        <p class="app-checker-prop"></p>
    </article>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">ID автомобиля:</label>
    <article class="col-md-9">
        <input value="{{ $car_id ?? '' }}" type="number" oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent())" min="6" name="car_id" class="MASK_ID_ELEM form-control">
        <p class="app-checker-prop"></p>
    </article>
</div>

<div class="form-group row">
    <label class="form-control-label col-md-3">Внесено в журнал ТО:</label>
    <article class="col-md-9">
        @include('profile.ankets.fields.added_to_dop', [
            'type_ankets' => $type_anketa,
            'field' => 'added_to_dop',
            'field_default_value' => ''
        ])
    </article>
</div>

<div class="form-group row">
    <label class="form-control-label col-md-3">Внесено в журнал МО:</label>
    <article class="col-md-9">
        @include('profile.ankets.fields.added_to_mo', [
            'type_ankets' => $type_anketa,
            'field' => 'added_to_mo',
            'field_default_value' => ''
        ])
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
        <label class="col-md-3 form-control-label">Количество выданных ПЛ:</label>
        <article class="col-md-9">
            <input type="number" value="{{ isset($count_pl) ? $count_pl : '' }}" required name="anketa[0][count_pl]" class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Период выданных ПЛ:</label>
        <article class="col-md-9">
            <input type="text" value="{{ isset($period_pl) ? $period_pl : '' }}" required name="anketa[0][period_pl]" class="form-control">
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>
