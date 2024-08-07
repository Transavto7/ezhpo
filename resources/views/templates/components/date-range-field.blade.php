<input
    @if($disabled) disabled @endif
    value="{{ $default_value }}"
    type="{{ $v['type'] }}" {{ $is_required }}
    name="{{ $k }}"
    data-label="{{ $v['label'] ?? $k }}"
    placeholder="{{ $v['label'] }}"
    data-field="{{ $model }}_{{ $k }}"
    @if ($v['type'] !== 'file') class="form-control {{ $v['classes'] ?? '' }}" @endif
    data-field-type="date-range-picker"
/>
