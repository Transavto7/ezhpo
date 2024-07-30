<div class="form-group">
    <label class="form-control-label">ID водителя:</label>
    <article>
        <input value="{{ $driver_id ?? '' }}"
               type="number"
               oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ 'false' }})"
               required min="6"
               name="driver_id"
               class="MASK_ID_ELEM form-control">
        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group">
        <label class="form-control-label">Дата и время осмотра:</label>
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

    @accessSetting('id_auto')
    <div class="form-group">
        <label class="form-control-label">ID автомобиля:</label>
        <article>
            <input value="{{ $car_id ?? '' }}"
                   type="number"
                   required
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent(), {{ 'false' }})"
                   min="6"
                   name="anketa[0][car_id]"
                   class="MASK_ID_ELEM form-control"
                   @accessSetting('id_auto_required') required @endaccessSetting/>
            <p class="app-checker-prop"></p>
        </article>
    </div>
    @endaccessSetting

    <div class="form-group">
        <label class="form-control-label">Тип осмотра:</label>
        <article>
            <select name="anketa[0][type_view]" required class="form-control">
                <option value="Предрейсовый/Предсменный" @if(strcasecmp($type_view ?? '', 'Предрейсовый/Предсменный') == 0) selected @endif>Предрейсовый/Предсменный</option>
                <option value="Послерейсовый/Послесменный" @if(strcasecmp($type_view ?? '', 'Послерейсовый/Послесменный') == 0) selected @endif>Послерейсовый/Послесменный</option>
            </select>
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>

<div class="form-group">
    <label class="form-control-label">Температура тела:</label>
    <article>
        <input type="number"
               step="0.1"
               value="{{ $t_people ?? '' }}"
               min="30"
               max="46"
               name="t_people"
               class="form-control">
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Проба на алкоголь:</label>
    <article>
        <select name="proba_alko"
                class="form-control"
                required
                onchange="updateAlcometerResult()">
            <option @if (($proba_alko ?? 'Отрицательно') === 'Отрицательно') selected @endif value="Отрицательно">Отрицательно</option>
            <option @if (($proba_alko ?? 'Отрицательно') === 'Положительно') selected @endif value="Положительно">Положительно</option>
        </select>
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Уровень алкоголя в выдыхаемом воздухе:</label>
    <article>
        <input type="number"
               step="0.01"
               min="0"
               value="{{ $alcometer_result ?? 0 }}"
               name="alcometer_result"
               class="form-control"
               onchange="updateProbaAlko()">
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Тест на наркотики:</label>
    <article>
        <select name="test_narko" required class="form-control">
            @php $test_narko = $test_narko ?? 'Не проводился';  @endphp
            <option @if ($test_narko === 'Не проводился') selected @endif value="Не проводился">
                Не проводился
            </option>
            <option @if ($test_narko === 'Отрицательно') selected @endif value="Отрицательно">
                Отрицательно
            </option>
            <option @if ($test_narko === 'Положительно') selected @endif value="Положительно">
                Положительно
            </option>
        </select>
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Мед показания:</label>
    <article>
        <select name="med_view" id="med_view" required class="form-control">
            @php $med_view = $med_view ?? 'В норме' @endphp
            <option @if ($med_view === 'В норме') selected @endif value="В норме">
                В норме
            </option>
            <option @if ($med_view === 'Отстранение') selected @endif value="Отстранение">
                Отстранение
            </option>
        </select>
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Показания тонометра:</label>
    <article>
        <input type="text"
               min="4"
               minlength="4"
               max="7"
               maxlength="7"
               placeholder="90/120 или 120/80 (пример)"
               name="anketa[0][tonometer]"
               value="{{ $tonometer ?? '' }}"
               class="form-control">
        <small></small>
    </article>
</div>

<div class="form-group">
    <label class="form-control-label">Пульс:</label>
    <article>
        <input type="number"
               maxlength="7"
               name="anketa[0][pulse]"
               value="{{ $pulse ?? '' }}"
               class="form-control">
        <small></small>
    </article>
</div>
