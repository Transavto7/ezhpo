@foreach($v['values'] as $optionK => $optionV)
    <option
        @if(in_array($optionV, $default_value) || in_array($optionK, $default_value))
            selected
        @endif
            value="{{ $optionK }}">
        {{ $optionV }}
    </option>
@endforeach
