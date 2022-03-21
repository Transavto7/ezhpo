@foreach($_GET as $k => $v)
    @if(is_array($_GET[$k]))
        @foreach($v as $vKey => $vValue)
            <input type="hidden" name="{{ $k }}[{{ $vKey }}]" value="{{ $vValue }}" />
        @endforeach
    @else
        <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
    @endif
@endforeach
