@switch(true)
    @case(strpos($url, 'youtube') !== false)
        @include('pages.driver-bdd.components.frames.youtube')
        @break
    @case(strpos($url, 'rutube') !== false)
        @include('pages.driver-bdd.components.frames.rutube')
        @break
    @default
@endswitch
