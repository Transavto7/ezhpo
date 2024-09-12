@php
    $url = $url ?? '';
    $youTubeURL = str_replace('https://www.youtube.com/watch?v=', '', $url);
@endphp

<iframe width="100%" height="350"
        srcdoc="@include('pages.driver-bdd.components.frames.youtube-player', ['youtube' => $youTubeURL])"
        allowfullscreen>
</iframe>

<a href="{{ $url }}">Посмотреть видео на YouTube</a>
