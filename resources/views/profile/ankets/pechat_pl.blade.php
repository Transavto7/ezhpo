<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group row">
    <label class="col-md-3 form-control-label">Название компании:</label>
    <article class="col-md-9">
        @php $company_fields['getFieldKey'] = 'hash_id'; @endphp
        @include('templates.elements_field', [
            'v' => $company_fields,
            'k' => 'company_id',
            'is_required' => 'required',
            'model' => 'Company',
            'default_value' => request()->get('company_id')
        ])

        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group row">
        <label class="col-md-3 form-control-label">Дата и время печати:</label>
        <article class="col-md-9">
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20" type="datetime-local" required value="{{ $default_current_date }}" name="anketa[0][date]" class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">ID водителя:</label>
        <article class="col-md-9">
            <input value="{{ $driver_id ?? '' }}" type="number"
                   oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())"
                   required min="6" name="anketa[0][driver_id]" class="MASK_ID_ELEM form-control">
            <div class="app-checker-prop"></div>
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Количество распечатанных ПЛ:</label>
        <article class="col-md-9">
            <input type="number" required name="anketa[0][count_pl]" class="form-control">
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>


