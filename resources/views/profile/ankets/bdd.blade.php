<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

        @include('profile.ankets.components.pvs')

        <div class="form-group">
            <label class="form-control-label">ID водителя:</label>
            <article>
                <input value="{{ $driver_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())"
                       required
                       min="6"
                       name="driver_id"
                       class="MASK_ID_ELEM form-control">
                <div class="app-checker-prop"></div>
            </article>
        </div>

        <div class="cloning" id="cloning-first">
            <div class="form-group">
                <label class="form-control-label">Дата проведения инструктажа:</label>
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
                <label class="form-control-label">Вид инструктажа:</label>
                <article>
                    <select name="anketa[0][type_briefing]" required class="form-control">
                        <option selected value="Вводный">Вводный</option>
                        <option value="Предрейсовый">Предрейсовый</option>
                        <option value="Сезонный (осенне-зимний)">Сезонный (осенне-зимний)</option>
                        <option value="Сезонный (весенне-летний)">Сезонный (весенне-летний)</option>
                        <option value="Специальный">Специальный</option>
                    </select>
                </article>
            </div>

            <div class="form-group">
                <label class="form-control-label">Подпись водителя:</label>
                <article>
                    <input type="text"
                           min="0"
                           minlength="0"
                           max="50"
                           maxlength="50"
                           name="anketa[0][signature]"
                           value="Подписано простой электронной подписью (ПЭП)"
                           class="form-control">
                </article>
            </div>

            <div class="anketa-delete"></div>
        </div>
    </div>
</div>
