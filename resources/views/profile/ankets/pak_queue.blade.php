@include('profile.ankets.components.pvs')

<input type="hidden" name="type_anketa" value="medic">
<input type="hidden" name="flag_pak" value="СДПО Р">

<div class="form-group row">
    <label class="col-md-3 form-control-label">ID водителя:</label>
    <article class="col-md-9">
        <input value="{{ $driver_id ?? '' }}" type="number" oninput="if(this.value.length >= 6) checkInputProp('hash_id', 'Driver', event.target.value, 'fio', $(event.target).parent())" required min="6" name="driver_id" class="MASK_ID_ELEM form-control">
        <div class="app-checker-prop"></div>
    </article>
</div>

<div class="cloning" id="cloning-first">
    <div class="form-group row">
        <label class="col-md-3 form-control-label">Дата и время осмотра:</label>
        <article class="col-md-9">
            <input min="1900-02-20T20:20"
                   max="2999-02-20T20:20" type="datetime-local" required value="{{ $default_current_date }}" name="anketa[0][date]" class="form-control">
        </article>
    </div>

    <div class="form-group row">
        <label class="col-md-3 form-control-label">Показания тонометра:</label>
        <article class="col-md-9">
            <input type="text" min="4" minlength="4" max="7" maxlength="7" placeholder="90/120 или 120/80 (пример)" name="anketa[0][tonometer]" value="{{ $tonometer ?? '' }}" class="form-control">
            <small>Недопустимо верхнее давление < 50 или > 220 , нижнее < 40 или > 160</small>
        </article>
    </div>

    <div class="anketa-delete"></div>
</div>

<div class="form-group row">
    <label class="col-md-3 form-control-label">Температура тела:</label>
    <article class="col-md-9">
        <input type="number" value="{{ $t_people ?? '' }}" name="t_people" class="form-control">
    </article>
</div>

@isset($photos)
    @if(!empty($photos))
        <div class="row">
            <div class="col-md-12">
                <p>Фотографии: </p>
            </div>
            @foreach(explode(',', $photos) as $photo)
                @php $photo_path = Storage::disk('public')->exists($photo) ? Storage::url($photo) : $photo; @endphp

                <a href="{{ $photo }}" data-fancybox class="col-md-3">
                    <img width="100%" src="{{ $photo }}" alt="photo" />
                </a>
            @endforeach
        </div>
    @endif
@endisset

@isset($videos)
    @if(!empty($videos))
        <div class="row">
            <div class="col-md-12">
                <p>Видео: </p>
            </div>
            @foreach(explode(',', $videos) as $video)
                <div class="col-md-4">
                    <video controls="controls" src="{{ $video }}" width="100%" height="100"></video>
                </div>
            @endforeach
        </div>
    @endif
@endisset

<hr>

@section('ankets_submit')
    <div class="text-center m-center">
        <label class="btn btn-success btn-sm">
            <i class="fa fa-check-circle"></i> Принять
            <input onchange="ANKETA_FORM.submit()" class="d-none" type="radio" value="Допущен" name="admitted" />
        </label>

        &nbsp;&nbsp;&nbsp;&nbsp;

        <label class="btn btn-danger btn-sm">
            <i class="fa fa-close"></i>
            Отклонить
            <input onchange="ANKETA_FORM.submit()" class="d-none" type="radio" value="Не допущен" name="admitted" />
        </label>
    </div>
@endsection

