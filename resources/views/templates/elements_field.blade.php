@php $default_value = isset($default_value) ? $default_value : ''; @endphp
@php $uniqueInputId = sha1(time() + rand(999,99999)); @endphp

@if($v['type'] !== 'select')
    <input
        @if(isset($v['saveToHistory']) && $v['type'] !== 'file')
            onchange="addFieldToHistory(event.target.value, '{{ $v['label'] }}');"
        @endif

        @if($v['type'] == 'file' && isset($v['resize']))
            id="croppie-input{{ $uniqueInputId }}"
        @endif

        value="{{ $default_value }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}"
        data-label="{{ isset($v['label']) ? $v['label'] : $k }}"
        placeholder="{{ $v['label'] }}"
        data-field="{{ $model }}_{{ $k }}"
        @if ($v['type'] !== 'file') class="form-control {{ isset($v['classes']) ? $v['classes'] : '' }}" @endif
    />

    @if($v['type'] == 'file' && $default_value)
        <div>
            <a data-fancybox href="{{ Storage::url($default_value) }}" target="_blank"><i class="fa fa-search"></i> Просмотреть файл</a>
            <br/>
            <a href="{{ route('deleteFileElement', [
                'id' => isset($element_id) ? $element_id : 0,
                'field' => $k,
                'model' => $model
            ]) }}"><i class="fa fa-trash"></i> Удалить файл</a>
        </div>
    @endif

    @if($v['type'] == 'file' && isset($v['resize']))
        <div style="display: none;" id="croppie-block{{ $uniqueInputId }}" class="croppie-block text-center">
            <input type="hidden" name="{{ $k }}_base64" id="croppie-result-base64{{ $uniqueInputId }}">
            <div class="croppie-demo" data-croppie-id="{{ $uniqueInputId }}"></div>
            <button type="button" data-croppie-id="{{ $uniqueInputId }}" class="btn croppie-save btn-sm btn-success">Сохранить обрезку</button>
            <button type="button" data-croppie-id="{{ $uniqueInputId }}" class="btn croppie-delete btn-sm btn-danger">Удалить фото</button>
        </div>
    @endif

@elseif ($v['type'] === 'select')
    @php
        $default_value = is_array($default_value) ? $default_value : explode(',', $default_value);
        $key = isset($v['getFieldKey']) ? $v['getFieldKey'] : 'id';
        $value = isset($v['getField']) ? $v['getField'] : 'name';
    @endphp
    <select
        @isset($v['saveToHistory'])
            onchange="addFieldToHistory(event.target.value, '{{ $v['label'] }}');"
        @endisset

        @if(($el->type_product ?? '') == 'Абонентская плата без реестров' && ($k == 'type_view'|| $k == 'type_anketa' ))
            @php
                $is_required = '';
            @endphp
        @endif
        @if(!is_array($v['values']))
            model="{{ $v['values'] }}"
            field-key="{{ $key }}"
            field="{{ $value }}"
        @endif

        @isset($v['multiple'])
            multiple="multiple"
            name="{{ $k }}[]"
        @else
            name="{{ $k }}"
        @endisset

        {{ $is_required }}
        data-label="{{ isset($v['label']) ? $v['label'] : $k }}"
        data-field="{{ $model }}_{{ $k }}"
        class="filled-select2 filled-select"
    >
        {{-- disabled selected --}}
        <option value="">Не установлено</option>

        @if(is_array($v['values']))
            @foreach($v['values'] as $optionK => $optionV)
                <option
                    @if(in_array($optionV, $default_value) || in_array($optionK, $default_value)) selected @endif
                value="{{ $optionK }}">
                    {{ $optionV }}
                </option>
            @endforeach
        @else
            @foreach(app("App\\" . $v['values'])::whereIn($key, $default_value)->get() as $option)
                <option selected value="{{ $option[$key] }}">
                    {{ $option[$value] }}
                </option>
            @endforeach
            @foreach(app("App\\" . $v['values'])::whereNotIn($key, $default_value)->limit(100)->get() as $option)
                <option value="{{ $option[$key] }}">
                    {{ $option[$value] }}
                </option>
            @endforeach
        @endif
    </select>
@endif
