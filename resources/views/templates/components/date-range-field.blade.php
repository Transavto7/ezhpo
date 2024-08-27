<div class="d-flex justify-content-between">
    <input
        value="{{ $default_value_start }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_start"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="С"
        data-field="{{ $model }}_{{ $k }}"
        class="form-control {{ $v['classes'] ?? '' }} bg-white"
        data-field-type="date-picker"
    />
    <input
        value="{{ $default_value_end }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_end"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="По"
        data-field="{{ $model }}_{{ $k }}"
        class="form-control ml-2 {{ $v['classes'] ?? '' }} bg-white"
        data-field-type="date-picker"
    />
</div>
