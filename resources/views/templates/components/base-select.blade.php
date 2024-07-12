@php
    $default_value = is_array($default_value)
        ? $default_value
        : explode(',', $default_value);
    $key = $v['getFieldKey'] ?? 'id';
    $value = $v['getField'] ?? 'name';
    $concatField = $v['concatField'] ?? false;
    $disabled = $disabled ?? false;
    if (($el->type_product ?? '') == 'Абонентская плата без реестров' && in_array($k, ['type_view', 'type_anketa'])) {
        $disabled = true;
    }
    if (($el->type_product ?? '') != 'Абонентская плата без реестров' && ($k == 'essence' )) {
        $disabled = true;
    }
@endphp

<select
    {{ $is_required }}
    @if($disabled)
        disabled
    @endif
    @isset($v['saveToHistory'])
        onchange="addFieldToHistory(event.target.value, '{{ $v['label'] }}');"
    @endisset
    @if(!is_array($v['values']))
        model="{{ $v['values'] }}"
        field-key="{{ $key }}"
        field="{{ $value }}"
    @endif
    @if($concatField)
        field-concat="{{ $concatField }}"
    @endif
    @isset($v['trashed'])
        field-trashed="true"
    @endisset
    @isset($v['multiple'])
        multiple="multiple"
        name="{{ $k }}[]"
    @else
        name="{{ $k }}"
    @endisset
        data-label="{{ $v['label'] ?? $k }}"
        data-field="{{ $model }}_{{ $k }}"
        class="filled-select2 filled-select @if($k === 'type_product') {{ 'type_product' }} @endif"
        data-allow-clear=true>
    <option value="">Не установлено</option>
    @if(is_array($v['values']))
        @include('templates.components.base-select-options.array')
    @elseif ($v['values'] === 'User')
        @include('templates.components.base-select-options.user')
    @else
        @include('templates.components.base-select-options.default')
    @endif
</select>
