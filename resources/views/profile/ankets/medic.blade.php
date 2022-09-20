<input type="hidden" name="type_anketa" value="{{ $type_anketa }}" />

@include('profile.ankets.components.pvs')

@include('profile.ankets.components.is_dop')

@if($is_dop)
    <div class="form-group row">
        <label class="form-control-label col-md-3">ID компании:</label>
        <article class="col-md-9">
            <input value="{{ $company_id ?? '' }}" required type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Company', event.target.value, 'name', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   min="5" name="company_id" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="form-group row">
        <label class="form-control-label col-md-3">ID водителя:</label>
        <article class="col-md-9">
            <input value="{{ $driver_id ?? '' }}" type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   min="6" name="driver_id" class="MASK_ID_ELEM form-control">
            <p class="app-checker-prop"></p>
        </article>
    </div>

    <div class="cloning" id="cloning-first">
        <div class="form-group row">
            <label class="col-md-3 form-control-label">Дата осмотра ПЛ:</label>
            <article class="col-md-9">
                <input min="1900-02-20T20:20"
                       max="2999-02-20T20:20" type="datetime-local"
                       @if (!isset($period_pl)) required @endif
                       oninput="changeFormRequire(this, 'pl-period')"
                       @isset ($date) value="{{ $default_current_date ?? '' }}" @endisset
                       name="anketa[0][date]" class="form-control pl-date">
            </article>
        </div>

        <div class="form-group row">
            <label class="col-md-3 form-control-label">Период выдачи ПЛ:</label>
            <article class="col-md-9">
                <input type="month"
                       @if (!isset($date)) required @endif
                       oninput="changeFormRequire(this, 'pl-date')"
                       value="{{ isset($period_pl) ? $period_pl : '' }}" name="anketa[0][period_pl]"
                       class="form-control pl-period">
            </article>
        </div>

        <div class="form-group row">
            <label class="form-control-label col-md-3">Тип осмотра:</label>
            <article class="col-md-9">
                <select name="anketa[0][type_view]" class="form-control">
                    @isset($type_view)
                        <option disabled selected value="{{ $type_view }}">{{ $type_view }}</option>
                    @endisset

                    <option selected value="Предрейсовый/Предсменный">Предрейсовый/Предсменный</option>
                    <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный</option>
                </select>
            </article>
        </div>

        <div class="anketa-delete"></div>
    </div>
@else
    <div class="form-group row">
        <label class="col-md-3 form-control-label">ID водителя:</label>
        <article class="col-md-9">
            <input value="{{ $driver_id ?? '' }}" type="number"
                   oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                   required min="6" name="driver_id" class="MASK_ID_ELEM form-control">
            <div class="app-checker-prop"></div>
        </article>
    </div>

    <div class="cloning" id="cloning-first">
        <div class="form-group row">
            <label class="col-md-3 form-control-label">Дата и время осмотра:</label>
            <article class="col-md-9">
                <input min="1900-02-20T20:20"
                       max="2999-02-20T20:20" type="datetime-local" required value="{{ $default_current_date }}"
                       name="anketa[0][date]" class="form-control">
            </article>
        </div>

        @accessSetting('id_auto')
            <div class="form-group row">
                <label class="form-control-label col-md-3">ID автомобиля:</label>
                <article class="col-md-9">
                    <input value="{{ $car_id ?? '' }}"
                           type="number" required
                           oninput="if(this.value.length >= 0) checkInputProp('hash_id', 'Car', event.target.value, 'gos_number', $(event.target).parent(), {{ ($is_dop && !($id ?? false)) ? 'true' : 'false' }})"
                           min="6"
                           name="anketa[0][car_id]"
                           class="MASK_ID_ELEM form-control"

                    @accessSetting('id_auto_required')
                        required
                    @endaccessSetting
                    />
                    <p class="app-checker-prop"></p>
                </article>
            </div>
        @endaccessSetting

        <div class="anketa-bordered">

            <div class="form-group row">
                <label class="form-control-label col-md-3">Тип осмотра:</label>
                <article class="col-md-9">
                    <select name="anketa[0][type_view]" required class="form-control">
                        @isset($type_view)
                            <option disabled selected value="{{ $type_view }}">{{ $type_view }}</option>
                        @endisset

                        <option selected value="Предрейсовый/Предсменный">Предрейсовый/Предсменный</option>
                        <option value="Послерейсовый/Послесменный">Послерейсовый/Послесменный</option>
                    </select>
                </article>
            </div>

        </div>

        <div class="anketa-delete"></div>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Температура тела:</label>
        <article class="col-md-9">
            <input type="number" step="0.1" value="{{ $t_people ?? '' }}" min="30" max="46" name="t_people"
                   class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Проба на алкоголь:</label>
        <article class="col-md-9">
            <select name="proba_alko" required class="form-control">
                @if($proba_alko ?? false)
                    <option
                        @if ($proba_alko === 'Отрицательно') selected @endif
                        value="Отрицательно"
                    >
                        Отрицательно
                    </option>

                    <option
                        @if ($proba_alko === 'Положительно') selected @endif
                        value="Положительно"
                    >
                        Положительно
                    </option>
                @else
                    <option selected value="Отрицательно">Отрицательно</option>
                    <option value="Положительно">Положительно</option>
                @endif

            </select>
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Тест на наркотики:</label>
        <article class="col-md-9">
            <select name="test_narko" required class="form-control">

                @if($test_narko ?? false)
                    <option
                        @if ($test_narko === 'Не проводился') selected @endif
                        value="Не проводился"
                    >
                        Не проводился
                    </option>

                    <option
                        @if ($test_narko === 'Отрицательно') selected @endif
                        value="Отрицательно"
                    >Отрицательно
                    </option>

                    <option
                        @if ($test_narko === 'Положительно') selected @endif
                        value="Положительно"
                    >
                        Положительно
                    </option>
                @else
                    <option selected value="Не проводился">Не проводился</option>
                    <option value="Отрицательно">Отрицательно</option>
                    <option value="Положительно">Положительно</option>
                @endif

            </select>
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Мед показания:</label>
        <article class="col-md-9">
            <select name="med_view" required class="form-control">
                @if($med_view ?? false)
                    <option
                        @if ($med_view === 'В норме') selected @endif
                        value="В норме"
                    >
                        В норме
                    </option>

                    <option
                        @if ($med_view === 'Отстранение') selected @endif
                        value="Отстранение"
                    >
                        Отстранение
                    </option>
                @else
                    <option selected value="В норме">В норме</option>
                    <option value="Отстранение">Отстранение</option>
                @endif

            </select>
        </article>
    </div>
@endif

@if(!$is_dop)
    <div class="form-group row">
        <label class="col-md-3 form-control-label">Показания тонометра:</label>
        <article class="col-md-9">
            <input type="text" min="4" minlength="4" max="7" maxlength="7" placeholder="90/120 или 120/80 (пример)"
                   name="anketa[0][tonometer]" value="{{ $tonometer ?? '' }}" class="form-control">
            <small>Недопустимо верхнее давление < 50 или > 220 , нижнее < 40 или > 160</small>
        </article>
    </div>
@endif


<div class="row">
    <div class="col-md-12">
        @if(isset($photos) || isset($videos))
            <p>Фотографии и видео:</p>
        @endif
    </div>
    @isset($photos)
        @if(!empty($photos))
            @foreach(explode(',', $photos) as $photo)
                @php $isUri = strpos($photo, 'sdpo.ta-7'); @endphp
                @php $photo_path = $isUri ? $photo : Storage::url($photo); @endphp

                <a href="{{ $photo_path }}" data-fancybox class="col-md-4">
                    <img width="100%" src="{{ $photo_path }}" alt="photo"/>
                </a>
            @endforeach
        @endif
    @endisset

    @isset($videos)
        @if(!empty($videos))
            @foreach(explode(',', $videos) as $video)
                <div class="col-md-4">
                    <video controls="controls" src="{{ $video }}" width="100%" height="100"></video>
                </div>
            @endforeach
        @endif
    @endisset
</div>

