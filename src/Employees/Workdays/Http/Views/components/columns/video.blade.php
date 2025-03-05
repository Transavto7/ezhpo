@php
    /** @var \Src\Employees\Workdays\Eloquent\Workday $workday */
    /** @var \App\FieldPrompt $field */
    $videos = explode(',', $workday[$field->field])
@endphp

@foreach($videos as $vK => $vV)
    @if($vK == 0)
        <a
            target="_blank"
            data-type="iframe"
            href="{{ route('showVideo', ['url' => $vV]) }}"
            data-fancybox="video_{{ $workday->id }}">
            <i class="fa fa-video-camera"></i>
            ({{ count($videos) }})
        </a>
    @else
        <a data-type="iframe" href="{{ $vV }}"
           target="_blank"
           data-fancybox="video_{{ $workday->id }}"></a>
    @endif
@endforeach
