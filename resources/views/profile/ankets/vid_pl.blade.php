<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-control-label">Название компании:</label>
            <article>
                @include('templates.elements_field', [
                    'v' => $company_fields,
                    'k' => 'company_name',
                    'is_required' => '',
                    'model' => 'Company',
                    'default_value' => $company_name ?? request()->get('company_name')
                ])

                <div class="app-checker-prop"></div>
            </article>
        </div>
        <div class="form-group">
            <label class="form-control-label">ID водителя:</label>
            <article>
                <input value="{{ $driver_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())"
                       min="6"
                       name="driver_id"
                       class="MASK_ID_ELEM form-control">
                <p class="app-checker-prop"></p>
            </article>
        </div>
        <div class="form-group">
            <label class="form-control-label">ID автомобиля:</label>
            <article>
                <input required
                       value="{{ $car_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent())"
                       min="6"
                       name="car_id"
                       class="MASK_ID_ELEM form-control">
                <p class="app-checker-prop"></p>
            </article>
        </div>
        <div class="form-group">
            <label class="form-control-label">Внесено в журнал ТО:</label>
            <article>
                @include('profile.ankets.fields.added_to_dop', [
                    'type_ankets' => $type_anketa,
                    'field' => 'added_to_dop',
                    'field_default_value' => ''
                ])
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">Внесено в журнал МО:</label>
            <article>
                @include('profile.ankets.fields.added_to_mo', [
                    'type_ankets' => $type_anketa,
                    'field' => 'added_to_mo',
                    'field_default_value' => ''
                ])
            </article>
        </div>
        <div class="cloning" id="cloning-first">
            <div class="form-group">
                <label class="form-control-label">Дата и время выдачи:</label>
                <article>
                    <input min="1900-02-20T20:20"
                           max="2999-02-20T20:20"
                           type="datetime-local"
                           required
                           value="{{ $default_current_date ?? '' }}"
                           name="anketa[0][date]"
                           class="form-control">
                </article>
            </div>

            <div class="form-group">
                <label class="form-control-label">Количество выданных ПЛ:</label>
                <article>
                    <input type="number"
                           value="{{ $count_pl ?? '' }}"
                           required
                           name="anketa[0][count_pl]"
                           class="form-control">
                </article>
            </div>

            <div class="form-group">
                <label class="form-control-label">Период выданных ПЛ:</label>
                <article>
                    <input type="text"
                           value="{{ $period_pl ?? '' }}"
                           required
                           name="anketa[0][period_pl]"
                           class="form-control">
                </article>
            </div>

            <div class="anketa-delete"></div>
        </div>
    </div>
</div>
