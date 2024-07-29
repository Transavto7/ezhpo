<div class="row">
    <div class="col-md-12">
        @include('profile.ankets.components.pvs')

        <input type="hidden" name="type_anketa" value="medic">
        <input type="hidden" name="flag_pak" value="СДПО Р">
        <input type="hidden" name="operator_id" value="{{ user()->id }}">

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

            <div class="anketa-delete"></div>
        </div>

        <div class="form-group">
            <label class="form-control-label">Температура тела:</label>
            <article>
                <input type="number"
                       step="0.1"
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
                <select name="proba_alko"
                        class="form-control"
                        required
                        onchange="updateAlcometerResult()">
                    <option @if (($proba_alko ?? false) === 'Отрицательно') selected @endif value="Отрицательно">Отрицательно</option>
                    <option @if ((($proba_alko ?? false) === 'Положительно') || !isset($proba_alko)) selected @endif value="Положительно">Положительно</option>
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
                       @if (($proba_alko ?? false) === 'Отрицательно') disabled @endif
                       onchange="updateProbaAlko()"
                >
            </article>
        </div>
    </div>

    @if(isset($photos) || isset($videos))
        <div class="col-md-12">
            <p>Фотографии и видео:</p>
        </div>
        @if(!empty($photos))
            @foreach(explode(',', $photos) as $photo)
                @php
                    $isUri = strpos($photo, 'sdpo.ta-7');
                    $photo_path = $isUri ? $photo : Storage::url($photo);
                @endphp

                <a href="{{ $photo_path }}" data-fancybox class="col-md-12">
                    <img width="100%" src="{{ $photo_path }}" alt="photo" />
                </a>
            @endforeach
        @endif

        @if(!empty($videos))
            @foreach(explode(',', $videos) as $video)
                <div class="col-md-12">
                    <video controls="controls" src="{{ $video }}" width="100%" height="100"></video>
                </div>
            @endforeach
        @endif
    @endif

    @section('ankets_submit')
        <div class="col-md-12">
            <div class="text-center m-center">
                <label class="btn btn-success btn-sm">
                    <i class="fa fa-check-circle"></i> Допущен
                    <input onclick="approveAdmitting()"
                           class="d-none"
                           type="radio"
                           value="Допущен"
                           name="admitted"/>
                </label>

                <label class="btn btn-secondary btn-sm">
                    <i class="fa fa-question"></i> Не идентифицирован
                    <input onchange="ANKETA_FORM.submit()"
                           class="d-none"
                           type="radio"
                           value="Не идентифицирован"
                           name="admitted"/>
                </label>

                <label class="btn btn-danger btn-sm">
                    <i class="fa fa-close"></i> Не допущен
                    <input onchange="ANKETA_FORM.submit()"
                           class="d-none"
                           type="radio"
                           value="Не допущен"
                           name="admitted" />
                </label>
            </div>
        </div>
    @endsection
</div>
