<div class="d-flex justify-content-between">
    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_max }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_max"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="Максимум"
        data-field="{{ $model }}_{{ $k }}"
        @if ($v['type'] !== 'file') class="form-control {{ $v['classes'] ?? '' }}" @endif
    />

    <input
        @if($disabled) disabled @endif
        value="{{ $default_value_min }}"
        type="{{ $v['type'] }}" {{ $is_required }}
        name="{{ $k }}_min"
        data-label="{{ $v['label'] ?? $k }}"
        placeholder="Минимум"
        data-field="{{ $model }}_{{ $k }}"
        @if ($v['type'] !== 'file') class="form-control ml-2 {{ $v['classes'] ?? '' }}" @endif
    />
</div>
