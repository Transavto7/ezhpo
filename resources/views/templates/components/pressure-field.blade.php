<div class="d-flex justify-content-between">
    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_min }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_min"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="От"
        data-field="{{ $model }}_{{ $k }}"
        class="form-control {{ $v['classes'] ?? '' }}"
        step="1"
        min="0"
    />

    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_max }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_max"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="До"
        data-field="{{ $model }}_{{ $k }}"
        class="form-control ml-2 {{ $v['classes'] ?? '' }}"
        step="1"
        min="0"
    />
</div>
