@php
    $url = $url ?? '';
    $ruTubeUrl = str_replace('/video/','/play/embed/', substr($url, 0, strrpos($url, '/?') ?: strlen($url)));
@endphp

<iframe width="100%"
        height="350"
        src="{{ $ruTubeUrl }}"
        frameBorder="0"
        allow="clipboard-write; autoplay"
        webkitAllowFullScreen
        mozallowfullscreen
        allowFullScreen>
</iframe>

<a href="{{ $url }}">Посмотреть видео на RUTUBE</a>
