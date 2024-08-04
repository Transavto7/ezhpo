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
                <img width="100%" src="{{ $photo_path }}" alt="photo"/>
            </a>
        @endforeach
    @endif

    @if(!empty($videos))
        @foreach(explode(',', $videos) as $video)
            <div class="col-md-12">
                <video controls="controls"
                       src="{{ $video }}"
                       width="100%"
                       height="100">
                </video>
            </div>
        @endforeach
    @endif
@endif
