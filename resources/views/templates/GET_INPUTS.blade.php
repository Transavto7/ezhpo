@foreach(request()->all() as $k => $v)
    @if(is_array($v))
        @foreach($v as $vKey => $vValue)
            <input type="hidden" name="{{ $k }}[{{ $vKey }}]" value="{{ $vValue }}" />
        @endforeach
    @else
        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
    @endif
@endforeach
