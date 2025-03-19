@php
    /** @var \Src\Employees\Workdays\Eloquent\Workday $workday */
    /** @var \App\FieldPrompt $field */
    $photos = explode(',', $workday[$field->field])
@endphp

@foreach($photos as $phI => $ph)
    @php $isUri = strpos($ph, 'sdpo.ta-7'); @endphp

    @if($phI == 0)
        <a href="{{ $isUri ? $ph : Storage::url($ph) }}"
           data-fancybox="gallery_{{ $workday->id }}">
            <i class="fa fa-camera"></i>({{ count($photos) }})
        </a>
    @else
        <a href="{{ $isUri ? $ph : Storage::url($ph) }}"
           data-fancybox="gallery_{{ $workday->id }}">
        </a>
    @endif
@endforeach
