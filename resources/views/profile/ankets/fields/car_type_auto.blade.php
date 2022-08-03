@php
    $default_value = isset($field_default_value) ? $field_default_value : '';
    $default_value = is_array($default_value) ? $default_value : explode(',', $default_value);
    $index = new \App\Http\Controllers\IndexController();

    $elements = $index->elements['Car']['fields']['type_auto']['values'];
@endphp

<select
    multiple="multiple"
    name="car_type_auto[]"
    class="filled-select2 filled-select"
>
        @foreach($elements as $type)
            <option
                @if(in_array($type, $default_value)) selected @endif
            value="{{ $type }}">
                {{ $type }}
            </option>
        @endforeach
</select>
