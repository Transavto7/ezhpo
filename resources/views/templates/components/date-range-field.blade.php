<div class="d-flex justify-content-between">
    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_start }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_start"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="С"
        data-field="{{ $model }}_{{ $k }}"
        @if ($v['type'] !== 'file') class="form-control {{ $v['classes'] ?? '' }}" @endif
        data-field-type="date-picker"
    />

    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_end }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_end"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="По"
        data-field="{{ $model }}_{{ $k }}"
        @if ($v['type'] !== 'file') class="form-control ml-2 {{ $v['classes'] ?? '' }}" @endif
        data-field-type="date-picker"
    />
</div>
