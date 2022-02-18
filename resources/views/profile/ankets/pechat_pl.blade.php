<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="form-group row">
    <label class="col-md-3 form-control-label">Название компании:</label>
    <article class="col-md-9">
        @include('templates.elements_field', [
            'v' => $company_fields,
            'k' => 'company_name',
            'is_required' => 'required',
            'model' => 'Company',
            'default_value' => request()->get('company_name')
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
        <label class="col-md-3 form-control-label">Количество распечатанных ПЛ:</label>
        <article class="col-md-9">
            <input type="number" required name="anketa[0][count_pl]" class="form-control">
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>
