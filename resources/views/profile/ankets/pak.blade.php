@include('profile.ankets.components.pvs')

<div class="row">
    <div class="col-md-12">

        <div class="form-group">
            <label class="form-control-label">Отправить в журнал осмотров (#{{ $id }}):</label>
            <article>
                <select name="type_anketa" class="form-control">
                    <option value="{{ $type_anketa }}">ПАК</option>
                    <option value="medic">Медицинский осмотр</option>
                </select>
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">ID водителя:</label>
            <article>
                <input value="{{ $driver_id ?? '' }}"
                       type="number"
                       oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())"
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
                    <small>Недопустимо верхнее давление < 50 или > 220 , нижнее < 40 или > 160</small>
                </article>
            </div>

            <div class="form-group">
                <label class="form-control-label">Тип осмотра:</label>
                <article>
                    <select name="anketa[0][type_view]" required class="form-control">
                        <option value="Предрейсовый" @if(($type_view ?? '') == "Предрейсовый") selected @endif>
                            Предрейсовый
                        </option>
                        <option value="Послерейсовый" @if(($type_view ?? '') == "Послерейсовый") selected @endif>
                            Послерейсовый
                        </option>
                        <option value="Предсменный" @if(($type_view ?? '') == "Предсменный") selected @endif>
                            Предсменный
                        </option>
                        <option value="Послесменный" @if(($type_view ?? '') == "Послесменный") selected @endif>
                            Послесменный
                        </option>
                    </select>
                </article>
            </div>

            <div class="form-group">
                <label class="form-control-label">Номер путевого листа:</label>
                <article>
                    <input value="{{ $number_list_road ?? '' }}" type="text" name="anketa[0][number_list_road]"
                           class="form-control">
                </article>
            </div>

            <div class="anketa-delete"></div>
        </div>

        <div class="form-group">
            <label class="form-control-label">Температура тела:</label>
            <article>
                <input type="number"
                       value="{{ $t_people ?? '' }}"
                       min="34"
                       max="45"
                       name="t_people"
                       class="form-control">
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">Проба на алкоголь:</label>
            <article>
                <select name="proba_alko" required class="form-control">
                    @isset($proba_alko)
                        <option selected value="{{ $proba_alko }}">{{ $proba_alko }}</option>
                        <option value="{{ $proba_alko == 'Положительно' ? 'Отрицательно' : 'Положительно' }}">
                            {{ $proba_alko == 'Положительно' ? 'Отрицательно' : 'Положительно' }}
                        </option>
                    @else
                        <option selected value="Отрицательно">Отрицательно</option>
                        <option value="Положительно">Положительно</option>
                    @endisset
                </select>
            </article>
        </div>

        <div class="form-group">
            <label class="form-control-label">Тест на наркотики:</label>
            <article>
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

        <div class="form-group">
            <label class="form-control-label">Мед показания:</label>
            <article>
                <select name="med_view" required class="form-control">
                    @isset($med_view)
                        <option disabled selected value="{{ $med_view }}">{{ $med_view }}</option>
                    @endisset

                    <option selected value="В норме">В норме</option>
                    <option value="Отстранение">Отстранение</option>
                </select>
            </article>
        </div>
    </div>
</div>
