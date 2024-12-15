<div class="modal fade" id="create-medic-form" tabindex="-1" aria-labelledby="create-medic-form-label"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-medic-form-label">Добавление МО</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="ANKETA_FORM">
                <form method="POST"
                      action="{{ route('forms.store') }}"
                      class="form-horizontal"
                      onsubmit="document.querySelector('#page-preloader').classList.remove('hide')"
                      enctype="multipart/form-data"
                      id="ANKETA_FORM">
                    @csrf

                    <input type="hidden" name="type_anketa" value="{{ \App\Enums\FormTypeEnum::MEDIC }}"/>

                    <input type="hidden" name="REFERER" value="{{ url()->current() }}">

                    @include('profile.ankets.components.pvs', ['points' => App\Point::getAll()])

                    <div class="form-group d-flex">
                        <input type="checkbox" id="medic-form-checkbox">
                        <label class="form-control-label mb-0 ml-2" for="medic-form-checkbox">Создать неполный осмотр</label>
                    </div>

                    <div class="full-medic-form d-none show">
                        <div class="form-group">
                            <label class="form-control-label">ID водителя:</label>
                            <article>
                                <input type="number"
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
                                    <input min="1970-01-01T00:00"
                                           max="2100-02-20T20:20"
                                           type="datetime-local"
                                           required
                                           name="date"
                                           class="form-control inspection-date">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Тип осмотра:</label>
                                <article>
                                    <select name="type_view" required class="form-control type-view">
                                        <option value="Предрейсовый/Предсменный" selected>Предрейсовый/Предсменный</option>
                                        <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный</option>
                                    </select>
                                    <p class="duplicate-indicator text-danger d-none" style="font-size: 0.7875rem">Осмотр с
                                        указанным водителем, датой и типом уже существует</p>
                                </article>
                            </div>

                            <div class="anketa-delete"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Температура тела:</label>
                            <article>
                                <input type="number"
                                       step="0.1"
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
                                    <option selected value="Отрицательно">Отрицательно
                                    </option>
                                    <option selected value="Положительно">Положительно
                                    </option>
                                </select>
                            </article>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Уровень алкоголя в выдыхаемом воздухе:</label>
                            <article>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       name="alcometer_result"
                                       class="form-control"
                                       onchange="updateProbaAlko()">
                            </article>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Тест на наркотики:</label>
                            <article>
                                <select name="test_narko" required class="form-control">
                                    <option selected value="Не проводился">Не проводился</option>
                                    <option value="Отрицательно">Отрицательно</option>
                                    <option value="Положительно">Положительно</option>
                                </select>
                            </article>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Мед показания:</label>
                            <article>
                                <select name="med_view" id="med_view" required class="form-control">
                                    <option value="В норме">В норме</option>
                                    <option value="Отстранение">Отстранение</option>
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
                                       name="tonometer"
                                       class="form-control">
                                <small></small>
                            </article>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">Пульс:</label>
                            <article>
                                <input type="number"
                                       maxlength="7"
                                       name="pulse"
                                       class="form-control">
                                <small></small>
                            </article>
                        </div>
                    </div>

                    <div class="short-medic-form d-none">
                        <div class="form-group">
                            <label class="form-control-label">ID компании:</label>
                            <article>
                                <input required
                                       type="number"
                                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                                       min="5"
                                       name="company_id"
                                       class="MASK_ID_ELEM form-control">
                                <p class="app-checker-prop"></p>
                            </article>
                        </div>

                        <div class="form-group">
                            <label class="form-control-label">ID водителя:</label>
                            <article>
                                <input type="number"
                                       oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ !($id ?? false) ? 'true' : 'false' }})"
                                       min="6"
                                       name="driver_id"
                                       class="MASK_ID_ELEM form-control">
                                <p class="app-checker-prop"></p>
                            </article>
                        </div>

                        <div class="cloning" id="cloning-first">
                            <div class="form-group">
                                <label class="form-control-label">Дата осмотра ПЛ:</label>
                                <article>
                                    <input oninput="changeFormRequire(this, 'pl-period')"
                                           required
                                           min="1900-02-20T20:20"
                                           max="2999-02-20T20:20"
                                           type="datetime-local"
                                           name="anketa[0][date]"
                                           class="form-control pl-date inspection-date">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Период выдачи ПЛ:</label>
                                <article>
                                    <input type="month"
                                           oninput="changeFormRequire(this, 'pl-date')"
                                           name="anketa[0][period_pl]"
                                           class="form-control pl-period">
                                </article>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Тип осмотра:</label>
                                <article>
                                    <select name="anketa[0][type_view]" class="form-control type-view">
                                        <option value="Предрейсовый/Предсменный" selected>Предрейсовый/Предсменный</option>
                                        <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный</option>
                                    </select>
                                    <p class="duplicate-indicator text-danger d-none" style="font-size: 0.7875rem">Осмотр с указанным водителем, датой и типом уже существует</p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a class="btn btn-secondary" data-dismiss="modal">Закрыть</a>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
