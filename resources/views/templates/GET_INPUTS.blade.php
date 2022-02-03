@foreach($_GET as $k => $v)
    <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? join(',', $v) : $v }}" />
@endforeach
